# **MANUAL DE UTILIZAÇÃO DO SISTEMA**

## **SGM – Sistema de Gestão de Manutenção**

Este manual descreve as funcionalidades, fluxos de operação e regras de negócio do SGM, servindo como guia oficial para usuários dos perfis **Gestor** e **Técnico**.

## **1\. Módulo do Gestor (Painel Administrativo)**

O perfil de Gestor é responsável pelo planejamento estratégico, controle de ativos físicos (infraestrutura) e distribuição/auditoria de chamados.

### **1.1. Gerenciamento de Infraestrutura (Blocos e Ambientes)**

Para manter a consistência geográfica dos chamados, o gestor deve manter o mapa físico da instituição atualizado:

* **Visualizar Blocos:** Na tela de Blocos, a tabela lista as estruturas cadastradas com suas respectivas descrições.  
* **Consultar Ambientes:** Ao clicar no botão Ver Ambientes, o sistema abrirá um painel dinâmico listando todas as salas, laboratórios ou divisões vinculadas àquele bloco específico de forma agrupada.  
* **Cadastrar/Editar:** Use a opção Editar (ícone de lápis) para corrigir nomes ou descrições de blocos. As alterações são validadas em tempo real.  
* **Excluir:** O botão Excluir abre uma janela de confirmação de segurança. **Atenção:** Só exclua blocos que não possuam chamados ativos atrelados a eles para manter o histórico do sistema.

### **1.2. Triagem e Distribuição de Chamados (Fluxo de Entrada)**

Ao receber um novo chamado aberto por um solicitante, o gestor deve acessar a tela de detalhes do chamado para qualificá-lo:

1. **Definição de Tipo de Serviço:** Selecione a categoria técnica correspondente (ex: *Elétrica, TI, Hidráulica*) no campo adequado.  
2. **Nível de Prioridade:** Classifique a urgência do problema entre *Baixa, Média, Alta* ou *Urgente*. Chamados marcados como "Alta" ou "Urgente" receberão alertas visuais piscantes na tela do técnico.  
3. **Atribuição de Responsável:** No menu de seleção de técnicos (que lista dinamicamente apenas profissionais ativos no sistema), escolha quem executará a tarefa.  
4. **Data de Previsão de Conclusão:** Insira a data limite para que o técnico se planeje.

### **1.3. Auditoria e Fechamento de Chamados (Fluxo de Saída)**

Após a atuação da equipe de campo, o gestor realiza o encerramento do ciclo de vida do chamado:

* **Revisão da Solução Técnica:** O gestor lê o relatório escrito pelo técnico detalhando o que foi feito.  
* **Auditoria de Tempo Gasto:** O sistema exibe o cálculo automático em minutos do tempo decorrido de trabalho.  
* **Data de Fechamento:** Ao finalizar, preencha o campo de data e hora de encerramento utilizando o calendário integrado.  
* **Gerenciamento de Imagens/Anexos:** Se a imagem enviada inicialmente não corresponder ao padrão ou se o chamado exigir a remoção por sigilo, utilize o botão Remover Imagem e adicione um novo arquivo se necessário antes de clicar em Salvar Alterações.

## **2\. Módulo do Técnico (Painel de Campo)**

O perfil de Técnico foi projetado com foco em usabilidade prática e agilidade, ideal para operação via dispositivos móveis no local da manutenção.

### **2.1. Operação com os Cards de Tarefas**

Ao fazer login, o técnico visualiza sua lista exclusiva de chamados em formato de cartões (*cards*):

* **Identificação Visual de Prioridades:** Fique atento às tags coloridas. Chamados de alta prioridade possuem animações de pulso para reter a atenção imediata.  
* **Consultar Anexos:** Cada card carrega a foto do problema de forma assíncrona (sem travar a tela). Clique na miniatura para abrir a imagem expandida no centro da tela.  
* **Ver Detalhes:** Clique em Ver Detalhes para abrir o histórico completo do chamado e ler informações adicionais inseridas pelo gestor.

### **2.2. Atualização de Status e Prazos (Painel de Atualizações Rápidas)**

Diferente de sistemas convencionais, o SGM garante que o técnico mude as informações no seu próprio ritmo, sem atualizações de página indesejadas enquanto trabalha:

1. No painel localizado à direita do card, mude o **Status da Tarefa** (*Agendado, Em Execução, Concluído*).  
2. Se necessário, ajuste a **Previsão de Conclusão** clicando no ícone de calendário. Você pode digitar ou selecionar a data livremente.  
3. **Gravação Consciente:** Suas alterações **só serão enviadas ao banco de dados** quando você clicar firmemente no botão verde **Atualizar**.  
4. Ao clicar em Atualizar, os campos ficarão levemente transparentes por um segundo, indicando o salvamento em segundo plano. Assim que concluído, a lista se reorganizará de forma automática.