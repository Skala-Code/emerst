# API de Cálculo - Ordem de Serviço

## Endpoint

**POST** `/api/service-orders/save-calculation`

Esta API permite salvar o ID/número do cálculo em uma ordem de serviço sem necessidade de autenticação.

## Requisição

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Body (JSON)
```json
{
  "service_order_id": 1,
  "calculation_number": "CALC-2025-001"
}
```

### Parâmetros

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| service_order_id | integer | Sim | ID da ordem de serviço |
| calculation_number | string | Sim | Número/ID do cálculo (máx 255 caracteres) |

## Resposta

### Sucesso (200)
```json
{
  "success": true,
  "message": "ID do cálculo salvo com sucesso",
  "data": {
    "service_order_id": 1,
    "calculation_number": "CALC-2025-001"
  }
}
```

### Erro de Validação (422)
```json
{
  "message": "The service order id field is required. (and 1 more error)",
  "errors": {
    "service_order_id": [
      "The service order id field is required."
    ],
    "calculation_number": [
      "The calculation number field is required."
    ]
  }
}
```

### Erro de Ordem de Serviço Não Encontrada (500)
```json
{
  "success": false,
  "message": "Erro ao salvar ID do cálculo",
  "error": "No query results for model [App\\Models\\ServiceOrder] 999"
}
```

## Exemplos de Uso

### cURL
```bash
curl -X POST http://seu-dominio.com/api/service-orders/save-calculation \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "service_order_id": 1,
    "calculation_number": "CALC-2025-001"
  }'
```

### PHP
```php
$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "http://seu-dominio.com/api/service-orders/save-calculation",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => json_encode([
    'service_order_id' => 1,
    'calculation_number' => 'CALC-2025-001'
  ]),
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Accept: application/json"
  ],
]);

$response = curl_exec($curl);
curl_close($curl);

$result = json_decode($response, true);
```

### JavaScript (Fetch)
```javascript
fetch('http://seu-dominio.com/api/service-orders/save-calculation', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    service_order_id: 1,
    calculation_number: 'CALC-2025-001'
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

### Python (requests)
```python
import requests

url = "http://seu-dominio.com/api/service-orders/save-calculation"
payload = {
    "service_order_id": 1,
    "calculation_number": "CALC-2025-001"
}
headers = {
    "Content-Type": "application/json",
    "Accept": "application/json"
}

response = requests.post(url, json=payload, headers=headers)
print(response.json())
```

## Notas

- Esta API **não requer autenticação**
- O campo `analyzed_calculation_id_fls` na tabela `service_orders` é usado para armazenar o número do cálculo
- A API valida se a ordem de serviço existe antes de salvar
- O número do cálculo pode conter letras, números e caracteres especiais
