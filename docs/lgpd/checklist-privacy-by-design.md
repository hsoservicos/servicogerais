# Checklist Privacy by Design — ServiceSaaS

## 1. Proativo, não reativo
- [x] LGPD considerada desde a concepção do projeto
- [x] Auditoria de compliance realizada (docs/auditoria/)
- [ ] Revisão periódica anual agendada

## 2. Privacidade como padrão
- [x] Dados mínimos coletados por formulário
- [x] Checkboxes de consentimento separados (marketing ≠ termos)
- [x] Senhas com bcrypt (12 rounds)
- [x] Prepared Statements contra SQL injection

## 3. Privacidade incorporada ao design
- [x] Multi-tenancy com tenant_id isolado
- [x] RBAC (admin, super_admin, viewer)
- [x] Audit logging de ações administrativas
- [x] Logs estruturados sem PII

## 4. Funcionalidade total — soma positiva
- [x] Exportação de dados (art. 18 LGPD)
- [x] Correção de dados (PUT /me)
- [x] Exclusão de dados (fila 15 dias)
- [x] Revogação de consentimento

## 5. Segurança de ponta a ponta
- [ ] HTTPS ativo (pendente Cloudflare)
- [x] Helmet headers de segurança
- [x] Rate limiting contra brute force
- [x] JWT com expiração 24h
- [ ] Criptografia em repouso (pendente MySQL TDE)

## 6. Visibilidade e transparência
- [x] Termos de Uso disponíveis (termos-de-uso.html)
- [x] Política de Privacidade (politica-privacidade.md)
- [x] Registro de Operações (registro-operacoes.md)
- [x] Política de Retenção (politica-retencao.md)
- [x] Plano de Resposta a Incidentes (plano-resposta-incidentes.md)

## 7. Respeito ao usuário
- [x] Consentimento granular (opt-in + communications + terms)
- [x] Canal DPO: privacidade@servicesaas.com
- [x] Direito de exclusão com confirmação
- [x] Portabilidade via export JSON
