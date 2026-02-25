-- ==========================================================
-- SCRIPT DE CRIAÇÃO - SISTEMA DE GESTÃO DE MANUTENÇÃO (SGM)
-- Escopo: Gestão de Chamados por Ambiente (Sem Ativos/Custos)
-- Banco de Dados: MySQL / MariaDB (Versão Produção)
-- ==========================================================

-- Configurações de Sessão para evitar erros durante a criação
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ----------------------------------------------------------
-- 0. CRIAÇÃO DO BANCO DE DADOS
-- ----------------------------------------------------------
CREATE DATABASE IF NOT EXISTS sgm_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sgm_db;

-- ----------------------------------------------------------
-- 1. TABELA DE USUÁRIOS (Atores do Sistema)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('solicitante', 'tecnico', 'gestor') NOT NULL DEFAULT 'solicitante',
    ativo TINYINT(1) DEFAULT 1,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario),
    UNIQUE INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. ESTRUTURA DE LOCALIZAÇÃO (Unidade -> Bloco -> Ambiente)
-- Nota: Unidades removidas do escopo (Single Site)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS blocos (
    id_bloco INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL, -- Ex: Bloco A, Administrativo
    descricao VARCHAR(200),
    PRIMARY KEY (id_bloco)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ambientes (
    id_ambiente INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL, -- Ex: Sala 101, Copa
    id_bloco INT NOT NULL,
    PRIMARY KEY (id_ambiente),
    CONSTRAINT fk_ambientes_blocos
        FOREIGN KEY (id_bloco)
        REFERENCES blocos (id_bloco)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 3. TIPOS DE SERVIÇO
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS tipos_servico (
    id_tipo INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    descricao VARCHAR(200),
    PRIMARY KEY (id_tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 4. TABELA PRINCIPAL: CHAMADOS (Ordens de Serviço)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS chamados (
    id_chamado INT NOT NULL AUTO_INCREMENT,
    
    -- Dados da Abertura
    descricao_problema TEXT NOT NULL,
    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('aberto', 'agendado', 'em_execucao', 'concluido', 'fechado', 'cancelado') DEFAULT 'aberto',
    
    -- Classificação
    prioridade ENUM('baixa', 'media', 'alta', 'urgente') DEFAULT 'baixa',
    data_previsao_conclusao DATE DEFAULT NULL,
    
    -- Dados do Fechamento
    solucao_tecnica TEXT,
    tempo_gasto_minutos INT DEFAULT NULL,
    data_fechamento DATETIME DEFAULT NULL,
    
    -- Chaves Estrangeiras
    id_solicitante INT NOT NULL,
    id_tecnico INT DEFAULT NULL,
    id_ambiente INT NOT NULL,
    id_tipo_servico INT NOT NULL,
    
    PRIMARY KEY (id_chamado),
    
    CONSTRAINT fk_chamados_solicitante
        FOREIGN KEY (id_solicitante)
        REFERENCES usuarios (id_usuario),
    
    CONSTRAINT fk_chamados_tecnico
        FOREIGN KEY (id_tecnico)
        REFERENCES usuarios (id_usuario)
        ON DELETE SET NULL, -- Se apagar técnico, chamado fica órfão mas existe
        
    CONSTRAINT fk_chamados_ambiente
        FOREIGN KEY (id_ambiente)
        REFERENCES ambientes (id_ambiente),
        
    CONSTRAINT fk_chamados_tipo
        FOREIGN KEY (id_tipo_servico)
        REFERENCES tipos_servico (id_tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 5. ANEXOS / EVIDÊNCIAS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS chamados_anexos (
    id_anexo INT NOT NULL AUTO_INCREMENT,
    caminho_arquivo VARCHAR(255) NOT NULL,
    tipo_anexo ENUM('abertura', 'conclusao') NOT NULL,
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_chamado INT NOT NULL,
    
    PRIMARY KEY (id_anexo),
    CONSTRAINT fk_anexos_chamados
        FOREIGN KEY (id_chamado)
        REFERENCES chamados (id_chamado)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 6. HISTÓRICO DE COMENTÁRIOS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS chamados_comentarios (
    id_comentario INT NOT NULL AUTO_INCREMENT,
    texto TEXT NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_chamado INT NOT NULL,
    id_usuario INT NOT NULL,
    
    PRIMARY KEY (id_comentario),
    
    CONSTRAINT fk_comentarios_chamado
        FOREIGN KEY (id_chamado)
        REFERENCES chamados (id_chamado)
        ON DELETE CASCADE,
        
    CONSTRAINT fk_comentarios_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- DADOS INICIAIS (SEED) - Opcional para popular a base
-- ==========================================================

-- Inserir Usuários Padrão (Senha '123456' hash exemplo)
INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES 
('Admin Gestor', 'admin@sgm.com', '$$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'gestor'),
('João Técnico', 'tecnico@sgm.com', '$$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'tecnico'),
('Maria Solicitante', 'usuario@sgm.com', '$$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'solicitante');

-- Inserir Tipos de Serviço Básicos
INSERT INTO tipos_servico (nome) VALUES 
('Elétrica'), ('Hidráulica'), ('Ar Condicionado'), ('Civil/Predial');

-- Inserir Blocos e Ambientes Exemplo
INSERT INTO blocos (nome) VALUES ('Bloco Administrativo'), ('Produção');
INSERT INTO ambientes (nome, id_bloco) VALUES ('Recepção', 1), ('Copa', 1), ('Linha 1', 2);

-- Restaurar configurações de sessão
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;




-- Inserindo 4 Técnicos
INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES 
('Carlos Eletricista', 'carlos.tecnico@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'tecnico'),
('Ana Hidráulica', 'ana.tecnica@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'tecnico'),
('Ricardo Ar-Condicionado', 'ricardo.tecnico@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'tecnico'),
('Roberto Civil', 'roberto.tecnico@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'tecnico');

-- Inserindo 3 Solicitantes
INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES 
('Fernanda Recepção', 'fernanda.usuario@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'solicitante'),
('Marcos Produção', 'marcos.usuario@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'solicitante'),
('Beatriz ADM', 'beatriz.usuario@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'solicitante');


-- Chamado 1: Problema Elétrico na Produção (Aberto)
-- Solicitante: Marcos Produção (ID 9) | Ambiente: Linha 1 (ID 3)
INSERT INTO `chamados` 
(`id_chamado`, `descricao_problema`, `status`, `prioridade`, `id_solicitante`, `id_tecnico`, `id_ambiente`, `id_tipo_servico`) 
VALUES 
(NULL, 'Painel de controle da Linha 1 apresentando oscilação de energia.', 'aberto', 'urgente', 9, NULL, 3, 1);

-- Chamado 2: Vazamento na Copa (Em Execução)
-- Solicitante: Beatriz ADM (ID 10) | Técnico: Ana Hidráulica (ID 5) | Ambiente: Copa (ID 2)
INSERT INTO `chamados` 
(`id_chamado`, `descricao_problema`, `status`, `prioridade`, `id_solicitante`, `id_tecnico`, `id_ambiente`, `id_tipo_servico`) 
VALUES 
(NULL, 'Sifão da pia da copa está com vazamento grave, molhando o armário.', 'em_execucao', 'media', 10, 5, 2, 2);

-- Chamado 3: Manutenção de Ar-Condicionado (Agendado)
-- Solicitante: Fernanda Recepção (ID 8) | Técnico: Ricardo Ar-Condicionado (ID 6) | Ambiente: Recepção (ID 1)
INSERT INTO `chamados` 
(`id_chamado`, `descricao_problema`, `status`, `prioridade`, `id_solicitante`, `id_tecnico`, `id_ambiente`, `id_tipo_servico`) 
VALUES 
(NULL, 'Limpeza preventiva dos filtros do ar-condicionado central.', 'agendado', 'baixa', 8, 6, 1, 3);


-- 1. Anexo para o Chamado 1 (Problema de abertura)
INSERT INTO `chamados_anexos` (`id_anexo`, `caminho_arquivo`, `tipo_anexo`, `id_chamado`) 
VALUES (NULL, 'imgs/limpeza_de_ar_condicionado.jpg', 'abertura', 12);

-- 2. Anexo para o Chamado 2 (Evidência de abertura)
INSERT INTO `chamados_anexos` (`id_anexo`, `caminho_arquivo`, `tipo_anexo`, `id_chamado`) 
VALUES (NULL, 'imgs/vazamento_copa.png', 'abertura', 11);

-- 3. Anexo para o Chamado 2 (Foto da conclusão/solução)
-- Útil para testar a exibição de múltiplas fotos no mesmo chamado
INSERT INTO `chamados_anexos` (`id_anexo`, `caminho_arquivo`, `tipo_anexo`, `id_chamado`) 
VALUES (NULL, 'imgs/lampada_trocada.jpg', 'conclusao', 9);