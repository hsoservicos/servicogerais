# 📄 Termos de Uso — ServiceSaaS

**Versão:** 1.0 | **Data:** 2026-07-27
**Contato:** `privacidade@seudominio.com.br`

---

## 1. Aceitação

Ao criar uma conta no ServiceSaaS, você declara ter lido, compreendido e aceitado estes Termos de Uso e a Política de Privacidade.

## 2. Definições

- **Plataforma:** Sistema web ServiceSaaS
- **Usuário/Prestador:** Contratante dos serviços da plataforma
- **Cliente Final:** Pessoa física ou jurídica cadastrada pelo Usuário
- **Dados Pessoais:** Conforme definição da LGPD (Lei 13.709/2018)

## 3. Serviços

A plataforma oferece:
- Cadastro e gestão de clientes
- Criação e envio de propostas comerciais
- Aprovação digital de propostas
- Processamento de pagamentos via Mercado Pago
- Geração de relatórios e dashboard

## 4. Obrigações do Usuário

- Fornecer dados cadastrais verdadeiros e atualizados
- Responder pela veracidade dos dados de seus clientes
- Obter consentimento ou base legal para cadastrar dados de terceiros
- Manter a confidencialidade de sua senha e credenciais
- Utilizar a plataforma de acordo com a legislação brasileira

## 5. Obrigações da Plataforma

- Proteger os dados pessoais com medidas de segurança técnicas e administrativas
- Processar os dados de acordo com as instruções do Usuário
- Notificar o Usuário em caso de incidente de segurança
- Atender aos direitos dos titulares previstos na LGPD
- Manter registro das operações de tratamento de dados

## 6. Acordo de Processamento de Dados (DPA)

**6.1 Partes:** ServiceSaaS (OPERADOR) e Usuário (CONTROLADOR)

**6.2 Finalidade:** Execução dos serviços de gestão de propostas e orçamentos

**6.3 Dados Tratados:** Nome, CPF/CNPJ, e-mail, telefone, WhatsApp, endereço, dados de pagamento

**6.4 Bases Legais:**
- Execução de contrato (Art. 7°, V)
- Legítimo interesse (Art. 7°, IX)
- Obrigação legal/regulatória (Art. 7°, II)
- Consentimento (Art. 7°, I)

**6.5 Medidas de Segurança:**
- Criptografia TLS 1.3 em trânsito
- Criptografia AES-256 em repouso (MySQL TDE)
- Autenticação via bcrypt + JWT
- Isolamento multi-tenancy
- Prepared Statements contra SQLi
- Output escaping contra XSS
- Rate limiting contra brute force
- Backups diários criptografados

**6.6 Sub-operadores:**
- Mercado Pago — processamento de pagamentos
- Cloudflare — CDN e segurança de rede
- Meta/WhatsApp — envio de mensagens

**6.7 Incidentes:**
- Notificação ao Controlador em até 48h
- Notificação à ANPD em até 72h (quando aplicável)

**6.8 Exclusão após Término:**
Ao término do contrato, os dados serão exportados (JSON) e disponibilizados por 30 dias para download. Após este prazo, serão anonimizados/excluídos, ressalvada retenção fiscal de 5 anos.

## 7. Direitos do Titular

O Usuário reconhece e garante que seus clientes finais têm direito a:
- Confirmar a existência de tratamento de dados
- Acessar seus dados pessoais
- Corrigir dados incompletos ou desatualizados
- Solicitar exclusão ou anonimização
- Revogar consentimento

Para exercer estes direitos, o titular deve contatar `privacidade@seudominio.com.br`.

## 8. Propriedade Intelectual

O Usuário mantém a propriedade total dos dados inseridos na plataforma. O ServiceSaaS detém os direitos sobre o software, sua interface e tecnologia.

## 9. Limitação de Responsabilidade

O ServiceSaaS não se responsabiliza por:
- Dados inseridos incorretamente pelo Usuário
- Falhas de terceiros (Mercado Pago, WhatsApp)
- Uso indevido da plataforma pelo Usuário
- Danos decorrentes de caso fortuito ou força maior

## 10. Prazo e Rescisão

- Vigência por prazo indeterminado
- Rescisão a qualquer momento pelo Usuário
- Rescisão pela plataforma em caso de violação dos termos

## 11. Lei Aplicável

Estes Termos regem-se pela legislação brasileira, em especial:
- Lei 13.709/2018 (LGPD)
- Código de Defesa do Consumidor (CDC)
- Marco Civil da Internet (Lei 12.965/2014)

## 12. Contato

**ServiceSaaS**
📧 `privacidade@seudominio.com.br`
📝 Formulário: `/privacidade.php`
