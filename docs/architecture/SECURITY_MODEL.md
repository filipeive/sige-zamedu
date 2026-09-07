# SECURITY_MODEL.md
## Camadas de Segurança e Estratégia de Testes

Pré-requisito de leitura: `DATA_SCOPING.md`.

---

## 1. O isolamento não vive numa camada só

"Esconder um botão não constitui isolamento" (mandato, Fase 3). A segurança tem de
existir em todas estas camadas, para todo módulo migrado (Fase 11):

```
Authentication
      ↓
SchoolContext (resolução do tenant/escola atual)
      ↓
Authorization / Policy
      ↓
Query Scope (SchoolScope / HierarchyScopeResolver)
      ↓
Service
      ↓
Controller
      ↓
View / API / Export
```

Uma falha em qualquer uma destas camadas é uma falha de isolamento, mesmo que as outras
estejam corretas.

## 2. Achados da auditoria relevantes para esta camada

Ver `MULTI_TENANCY_AUDIT.md` secção 6 — riscos já identificados a fechar na Fase 3:
`License::first()`, rotas com `{id}` direto sem verificação de pertença, exports sem
scope. Não repetir aqui — tratar como checklist de entrada da Fase 3.

## 3. Regra de manipulação de URL (Fase 8/12 do mandato)

Proibido: um utilizador conseguir aceder a `/schools/999/...` ou `/students/123` (de
outra escola) só por alterar o ID na URL, mesmo que não tenha link nenhum para lá na
interface. Toda rota que recebe um ID de recurso tem de verificar, no controller ou via
Policy, que esse recurso pertence ao `school_id` (ou está dentro do scope territorial)
do utilizador autenticado — independentemente do `SchoolScope` global já filtrar por
defeito (defesa em profundidade: o scope global protege listagens, mas um
`Model::find($id)` direto ainda pode escapar ao scope se mal implementado).

## 4. Testes obrigatórios (Fase 12 do mandato) — matriz de isolamento

Para cada entidade de negócio migrada (aluno, professor, turma, nota, pagamento,
relatório, documento), criar testes que confirmem que um utilizador da Escola A
**não consegue**, através de nenhuma interface:

- visualizar um registo da Escola B;
- editar um registo da Escola B;
- apagar um registo da Escola B;
- exportar um registo da Escola B;
- obter um registo da Escola B via API;
- obter um registo da Escola B através de filtros manipulados;
- obter um registo da Escola B alterando IDs na URL;
- obter um registo da Escola B através de relações indiretas (ex: aceder a uma nota via
  `/students/{id_de_outra_escola}/grades`).

Testar também isolamento **administrativo territorial**:
- um `zip_admin` não pode consultar dados de outro ZIP;
- um `district_admin` não pode consultar dados de outro distrito;
- um `province_admin` não pode consultar dados de outra província.

Modelo de teste (Pest/PHPUnit, adaptar ao existente em `tests/Feature/`):
```php
test('utilizador da escola A não consegue ver aluno da escola B', function () {
    $schoolA = School::factory()->create();
    $schoolB = School::factory()->create();
    $studentB = Student::factory()->for($schoolB)->create();
    $userA = User::factory()->for($schoolA)->create();

    actingAs($userA)
        ->get(route('students.show', $studentB))
        ->assertStatus(404); // ou 403, decidir e documentar consistentemente
});
```

## 5. API e exports seguem a mesma regra de autorização que a web (Fase 14)

Não criar uma regra de segurança para Blade e outra, diferente, para API — o domínio de
autorização (`SchoolScope`, `HierarchyScopeResolver`, Policies) é o mesmo,
independentemente da camada de apresentação.

## 6. Compatibilidade multi-motor de base de dados

Não usar comandos raw exclusivos de um motor de BD nas migrações. Migrações e testes
devem correr tanto no motor de testes (SQLite, se for o caso — confirmar em
`phpunit.xml`/`.env.testing`) como no motor de produção.
