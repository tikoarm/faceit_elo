FROM python:3.11-slim

WORKDIR /faceit_app

COPY faceit_app/ .
COPY requirements.txt .

RUN pip install --no-cache-dir -r requirements.txt

CMD ["python", "main.py"]