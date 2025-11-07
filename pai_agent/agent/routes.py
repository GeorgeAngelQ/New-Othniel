
import requests
import os, json, re, unicodedata
from flask import request, jsonify
from . import agent_bp

FAQ = {
    "compra": "Puedes buscar por color/talla/precio y añadir al carrito. Ej: 'polo talla M menos de 60', 'pantalon color azul marino'. Luego ve al carrito para pagar (tarjeta, Yape/Plin o criptomonedas).",
    "cambios": "Cambios/devoluciones: dentro de 7 días con boleta y prenda en buen estado. Ropa interior y ofertas no aplican.",
    "envios": "Envíos: Lima 24-48h, provincias 2-5 días. Costo según distrito/ciudad. Rastreo por correo/SMS.",
    "horarios": "Horario: Lun–Sáb 9:00–19:00. Soporte por chat y correo.",
}


def load_products():
    file_path = os.path.join(os.path.dirname(__file__), '../catalog.json')

    with open(file_path, 'r', encoding='utf-8') as f:
        return json.load(f)

# ---------- Normalización y matching flexible ----------
def normalize(t: str):
    t = (t or '').lower().strip()
    t = ''.join(c for c in unicodedata.normalize('NFD', t) if unicodedata.category(c) != 'Mn')
    t = re.sub(r'[^a-z0-9ñ\s]', ' ', t)
    return re.sub(r'\s+', ' ', t).strip()

STOPWORDS = set("""
de del la el los las un una unos unas por para con en y o a
que menos hasta talla color precio busco buscar muestrame mostrar mostrarme
quiero necesito ver encontrar
""".split())

def singular(w: str):
    return w[:-1] if len(w) > 3 and w.endswith('s') else w

def terms_from_query(q: str):
    words = [singular(w) for w in normalize(q).split()]
    words = [w for w in words if w not in STOPWORDS and not w.isdigit()]
    return words

def match_loose(product_txt: str, query_terms):
    if not query_terms:
        return True
    hits = sum(1 for t in query_terms if t in product_txt)
    return hits >= 1

# ---------- Filtros ----------
def parse_filters(msg):
    m_precio = re.search(r'(?:menos de|hasta)\s*(\d+(?:\.\d+)?)', msg, re.I)
    m_talla  = re.search(r'\btalla\s*([a-z0-9]+)\b', msg, re.I)
    m_color  = re.search(r'\bcolor\s*([a-záéíóúüñ ]+)\b', msg, re.I)
    m_cat    = re.search(r'\b(camisa|casaca|polera|polo|pantal[oó]n|chompa|chaqueta|jogger|short|sudadera|bermuda|cargo|chaleco|entrenamiento)\b', msg, re.I)

    f = {
        'precioMax': float(m_precio.group(1)) if m_precio else None,
        'talla': (m_talla.group(1) if m_talla else '').upper() or None,
        'color': normalize(m_color.group(1)) if m_color else None,
        'categoria': (m_cat.group(1).lower() if m_cat else None),

        # flags de “explícito”
        '_precio_exp': bool(m_precio),
        '_talla_exp':  bool(m_talla),
        '_color_exp':  bool(m_color),
        '_cat_exp':    bool(m_cat),
    }
    return f

def by_categoria(p, cat):
    if not cat:
        return True
    name = normalize(p.get('name',''))
    return cat in name

# ---------- Ruta principal ----------
def by_categoria(p, cat):
    if not cat:
        return True
    return cat in normalize(p.get('name', ''))


# =====================================================
#           RUTA PRINCIPAL DEL AGENTE /chat
# =====================================================
@agent_bp.route('/chat', methods=['POST'])
def chat():
    data = request.get_json(silent=True) or {}
    raw = data.get("message", "")
    msg = json.dumps(raw) if isinstance(raw, dict) else str(raw).strip()
    low = msg.lower()

    # Saludos
    if re.search(r'\b(hola|buenas|hey|que tal|qué tal|holi|alo|saludos)\b', low):
        return jsonify({"type":"chat","data":"¡Hola! 👋 Puedo ayudarte a buscar prendas y añadir al carrito."})

    # Botones del menú (FAQ)
    m_opt = re.search(r'\bopcion:(compra|estado|cambios|envios|horarios)\b', low)
    if m_opt:
        opt = m_opt.group(1)
        if opt == "estado":
            return jsonify({"type":"chat","data":"Envíame tu número de orden. Ej: 'estado 12345'."})
        return jsonify({"type":"chat","data": FAQ.get(opt, "¿En qué puedo ayudarte?")})


    # Consultar estado del pago
    m_estado = re.search(r'\b(estado|orden|pago)\s*(\d{3,})\b', low)
    if m_estado:
        order_id = m_estado.group(2)

        # ⚠️ Aquí solo devolvemos placeholder
        return jsonify({"type":"status","data":{
            "status":"PENDING",
            "method":"Coingate",
            "order_id": order_id
        }})


    # =======================
    # BUSQUEDA DE PRODUCTOS
    # =======================
    if any(w in low for w in ['buscar','muestrame','polo','polera','camisa','casaca','pantal','jogger','chompa','joggers','short','sudadera','bermuda','cargo','chaleco','entrenamiento','jean']):
        products = load_products()
        q_terms = terms_from_query(msg)
        f = parse_filters(msg)

        def apply_filters(items, fobj):
            res = [p for p in items if by_categoria(p, fobj['categoria'])]
            if fobj['talla']:
                res = [p for p in res if fobj['talla'] in (p.get('talla') or [])]
            if fobj['color']:
                color_q = singular(fobj['color'])
                res = [p for p in res if singular(normalize(p.get('color',''))) == color_q]
            if fobj['precioMax'] is not None:
                def safe_price(p):
                    try:
                        return float(p.get('price', 0))
                    except:
                        return 0

                res = [p for p in res if safe_price(p) <= fobj['precioMax']]

            return res

        base = [p for p in products if match_loose(normalize(p.get("name","")), q_terms)]
        results = apply_filters(base, f)

        if not results:
            return jsonify({"type":"chat","data":"No encontré coincidencias. Intenta: ‘polo M hasta 60’ o ‘casaca negra’."})

        payload = [{
            "id":p.get("id_product"),
            "nombre":p.get("name"),
            "precio":p.get("price"),
            "moneda":"PEN",
            "tallas":p.get("talla",[]),
            "colores":[p.get("color","")],
            "img":p.get("image_url","")
        } for p in results[:12]]

        return jsonify({"type":"products","data":payload})


    # ===================
    # AÑADIR AL CARRITO
    # ===================
    if "carrito" in low or "agrega" in low or "añade" in low:
        return jsonify({"type":"cart","data":{"ok":True}})

    return jsonify({"type":"chat","data":"Puedo ayudarte a buscar prendas por talla/color/precio.\nEj: ‘polo M hasta 60’, ‘jogger azul’, ‘CAM-01’."})
