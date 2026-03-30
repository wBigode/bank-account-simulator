# Bank Account Simulator

## Requisitos

- Docker e Docker Compose

## Como subir o projeto

```bash
docker compose up
```

O servidor estará disponível em `http://localhost:9501`.

## Como executar os testes

```bash
docker compose exec bank composer test
```

## Rotas

### POST /reset

Limpa todas as contas.

**Response:** `200 OK`

---

### GET /balance?account_id={id}

Retorna o saldo de uma conta.

**Response sucesso:** `200 {saldo}`

**Response conta inexistente:** `404 0`

---

### POST /event

Processa operações bancárias.

#### Depósito

```json
{"type": "deposit", "destination": "100", "amount": 10}
```

**Response:** `201 {"destination": {"id": "100", "balance": 10}}`

#### Saque

```json
{"type": "withdraw", "origin": "100", "amount": 5}
```

**Response sucesso:** `201 {"origin": {"id": "100", "balance": 5}}`

**Response conta inexistente ou saldo insuficiente:** `404 0`

#### Transferência

```json
{"type": "transfer", "origin": "100", "destination": "300", "amount": 15}
```

**Response sucesso:** `201 {"origin": {"id": "100", "balance": 0}, "destination": {"id": "300", "balance": 15}}`

**Response conta de origem inexistente ou saldo insuficiente:** `404 0`
