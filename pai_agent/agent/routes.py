
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
@agent_bp.route('/chat', methods=['POST'])
def chat():
    data = request.get_json(silent=True) or {}

    raw = data.get("message", "")

    # Si el frontend envía un objeto en vez de texto -> conviértelo en string
    if isinstance(raw, dict):
        msg = json.dumps(raw)
    else:
        msg = str(raw).strip()

    low = msg.lower()

    # Saludos
    if re.search(r'\b(hola|buenas|buenos dias|buenas tardes|buenas noches|hey|que tal|qué tal|holi|alo|saludos)\b', low):
        return jsonify({"type":"chat","data":"¡Hola! 👋 Soy el asistente de Othniel. Puedo ayudarte a buscar prendas, filtrar por talla/color/precio y añadir al carrito. Ejemplos: ‘polo menos de 60’, ‘jogger color azul’."})

    # Ayuda
    if re.search(r'\b(ayuda|help|\?)\b', low):
        return jsonify({"type":"chat","data":"Prueba: ‘polo talla M hasta 60’, ‘casaca color negro’, ‘jogger menos de 60’."})

    products = load_products()



    # --- Opciones del menú rápido ---
    # Llega desde el frontend como: opcion:compra / opcion:estado / opcion:cambios / opcion:envios / opcion:horarios
    m_opt = re.search(r'\bopcion:(compra|estado|cambios|envios|horarios)\b', low)
    if m_opt:
        opt = m_opt.group(1)
        if opt == "estado":
            # Si no envían número, pedimos uno
            return jsonify({"type":"chat","data":"Para consultar el estado, envíame tu número de orden. Ej: 'estado 12345'."})
        # Resto son FAQs
        return jsonify({"type":"chat","data": FAQ.get(opt, "¿En qué puedo ayudarte?")})

    # --- Estado de pago por número de orden (entrada libre) ---
    # Reconoce: "estado 12345", "orden 12345", "pago 12345"
    m_estado = re.search(r'\b(estado|orden|pago)\s*(\d{3,})\b', low)
    if m_estado:
        order_id = m_estado.group(2)
        # Placeholder: aquí podrías consultar CoinGate con su API y devolver el estado real
        return jsonify({"type":"status","data":{"status":"PENDING","method":"Coingate","order_id":order_id}})




    # --------- Búsqueda de catálogo ----------

    if any(w in low for w in ['buscar','muestrame','muestráme','polo','polos','polera','poleras','camisa','casaca','pantal','pantalon','pantalón','chompa','jogger','chaqueta']):
        q_terms = terms_from_query(msg)
        f = parse_filters(msg)

        def apply_filters(items, fobj):
            res = [p for p in items if by_categoria(p, fobj['categoria'])]
            if fobj['talla']:
                res = [p for p in res if fobj['talla'] in p.get('talla',[])]
            if fobj['color']:
                color_q = singular(fobj['color'])
                res = [p for p in res if singular(normalize(p.get('color',''))) == color_q]
            if fobj['precioMax'] is not None:
                res = [p for p in res if (p.get('price') or 0) <= fobj['precioMax']]
            return res

        # 1) Texto “suave”
        base = []
        for p in products:
            prod_txt = normalize(f"{p.get('id_product','')} {p.get('name','')} {p.get('color','')}")
            if match_loose(prod_txt, q_terms):
                base.append(p)

        # 2) Aplicar filtros tal cual
        results = apply_filters(base, f)
        relaxed = False
        note = None

        # 3) Relajaciones controladas (SOLO si NO fueron explícitas)
        if not results and f.get('color') and not f.get('_color_exp'):
            f = dict(f); f['color'] = None
            results = apply_filters(base, f); relaxed = True

        if not results and f.get('precioMax') is not None and not f.get('_precio_exp'):
            f = dict(f); f['precioMax'] = f['precioMax'] * 1.10
            results = apply_filters(base, f); relaxed = True

        if not results and f.get('categoria') and not f.get('_cat_exp'):
            f = dict(f); f['categoria'] = None
            results = apply_filters(base, f); relaxed = True

        # 4) Si hubo filtros explícitos y sigue vacío -> mensaje claro
        if not results and (f.get('_color_exp') or f.get('_precio_exp') or f.get('_talla_exp') or f.get('_cat_exp')):
            motivos = []
            pf = parse_filters(msg)  # volver a leer valores originales para el mensaje
            if pf.get('_color_exp'):  motivos.append(f"color '{pf.get('color') or ''}'")
            if pf.get('_talla_exp'):  motivos.append(f"talla '{pf.get('talla') or ''}'")
            if pf.get('_precio_exp'): motivos.append(f"precio ≤ {pf.get('precioMax') or ''}")
            if pf.get('_cat_exp'):    motivos.append(f"categoría '{pf.get('categoria') or ''}'")
            detalle = ", ".join(motivos)
            return jsonify({"type":"chat","data":f"No encontré coincidencias exactas para {detalle}. Prueba con otro color/talla o ajusta el precio."})

        # 5) Último recurso: mejores matches (solo si no había filtros explícitos)
        if not results:
            def score(p):
                prod_txt = normalize(f"{p.get('id','')} {p.get('name','')} {p.get('color','')}")
                hits = sum(1 for t in q_terms if t in prod_txt)
                bonus = 0
                if f.get('talla') and f['talla'] in p.get('talla',[]): bonus += 1
                if f.get('color') and singular(normalize(p.get('color',''))) == singular(f['color']): bonus += 1
                if f.get('precioMax') is not None and (p.get('price') or 0) <= f['precioMax']: bonus += 1
                return hits*2 + bonus
            ranked = sorted(products, key=score, reverse=True)
            results = ranked[:3] if ranked else []
            if results:
                relaxed = True
                note = "Lo más cercano a tu búsqueda."

        if not results:
            return jsonify({"type":"chat","data":"No encontré coincidencias. Ej.: ‘polo talla M hasta 60’ o ‘polera negra’."})

        payload = [{
            "id":p.get("id_product"), "nombre":p.get("name"), "precio":p.get("price"), "moneda":"PEN",
            "tallas":p.get("talla",[]), "colores":[p.get("color","")], "img":p.get("image_url","")
        } for p in results[:12]]

        resp = {"type":"products","data":payload}
        if relaxed and note:
            resp["note"] = note
        return jsonify(resp)

    # --------- Detalle por ID ----------
    m_id = re.search(r'\b([A-Z]{3}-\d{2})\b', low.upper())
    if m_id:
        pid = m_id.group(1)
        p = next((x for x in products if (x.get("id_product") or "").upper() == pid), None)
        if not p:
            return jsonify({"type":"product","data":None})
        return jsonify({"type":"product","data":{
            "id":p.get("id_product"), "nombre":p.get("name"), "precio":p.get("price"), "moneda":"PEN",
            "tallas":p.get("talla",[]), "colores":[p.get("color","")], "img":p.get("image_url","")
        }})

    # --------- Añadir al carrito (lo hace el front con addToCart) ----------
    if any(w in low for w in ['añade','agrega','carrito']):
        return jsonify({"type":"cart","data":{"ok":True,"note":"añadido desde UI del cliente"}})

    # --------- Estado de pago (placeholder) ----------
    if any(w in low for w in ['estado','orden','pago']):
        return jsonify({"type":"status","data":{"status":"PENDING","method":"Coingate"}})

    # Fallback
    return jsonify({"type":"chat","data":"Puedo ayudarte a buscar prendas por talla, color y precio. Ej.: ‘polo M hasta 60’, ‘casaca negra L’, ‘CAM-01’."})
