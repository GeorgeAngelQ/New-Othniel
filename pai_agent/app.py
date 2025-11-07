from flask import Flask
from agent import agent_bp
from flask_cors import CORS

app = Flask(__name__)

CORS(
    app,
    resources={r"/agent/*": {"origins": "*"}},
    supports_credentials=True,
    allow_headers=["Content-Type"],
    methods=["GET", "POST", "OPTIONS"]
)
app.register_blueprint(agent_bp, url_prefix="/agent")
@app.after_request
def apply_cors(response):
    response.headers["Access-Control-Allow-Origin"] = "*"
    response.headers["Access-Control-Allow-Methods"] = "GET, POST, OPTIONS"
    response.headers["Access-Control-Allow-Headers"] = "Content-Type, Authorization"
    return response

if __name__ == "__main__":
    app.run(port=5000, debug=True)
