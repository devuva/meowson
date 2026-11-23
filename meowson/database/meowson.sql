-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08/11/2026 às 13:41
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `meowson`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `doacoes`
--

CREATE TABLE `doacoes` (
  `id` int(11) NOT NULL,
  `doador_id` int(11) DEFAULT NULL,
  `recebedor_id` int(11) NOT NULL,
  `valor` decimal(8,2) NOT NULL,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp(),
  `forma_de_pag` enum('pix','credito','debito') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fotos_gato`
--

CREATE TABLE `fotos_gato` (
  `id` int(11) NOT NULL,
  `gato_id` int(11) NOT NULL,
  `arquivo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gatos`
--

CREATE TABLE `gatos` (
  `usuario_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `raca` varchar(30) DEFAULT 'SRD',
  `idade` int(11) NOT NULL,
  `sexo` enum('F','M') NOT NULL,
  `descricao` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `vacinas` text DEFAULT 'nenhuma',
  `doencas` text DEFAULT 'nenhuma',
  `motivo_recusa` text DEFAULT NULL,
  `status` enum('disponivel','em_analise','recusado','adotado') NOT NULL DEFAULT 'em_analise',
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gatos`
--

INSERT INTO `gatos` (`usuario_id`, `id`, `nome`, `raca`, `idade`, `sexo`, `descricao`, `observacoes`, `vacinas`, `doencas`, `motivo_recusa`, `status`, `data_criacao`) VALUES
(1, 1, 'lola', 'SRD', 12, 'F', NULL, NULL, 'nenhuma', 'nenhuma', NULL, 'disponivel', '2026-10-31 12:09:11'),
(1, 2, 'puka', 'SRD', 40, 'F', NULL, NULL, 'nenhuma', 'nenhuma', NULL, 'disponivel', '2026-10-31 12:09:11'),
(1, 3, 'fustrico', 'SRD', 70, 'M', NULL, NULL, 'nenhuma', 'nenhuma', NULL, 'adotado', '2026-10-31 12:09:11'),
(1, 4, 'cuscão', 'SRD', 30, 'M', NULL, NULL, 'nenhuma', 'nenhuma', NULL, 'adotado', '2026-10-31 14:52:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `mensagem` text NOT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT 0,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacoes_adocao`
--

CREATE TABLE `solicitacoes_adocao` (
  `id` int(11) NOT NULL,
  `gato_id` int(11) NOT NULL,
  `adotante_id` int(11) NOT NULL,
  `status` enum('pendente','aprovada','recusada','encerrada') DEFAULT NULL,
  `motivo_recusa` text DEFAULT NULL,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp(),
  `mensagem_adotante` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cpf_cnpj` varchar(14) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `cep` varchar(8) NOT NULL,
  `logradouro` varchar(100) NOT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `perfil` enum('adotante','ong','protetor','admin') NOT NULL,
  `motivo_recusa` text DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `situacao_cadastro` enum('ativo','pendente','recusado') NOT NULL DEFAULT 'pendente',
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `cpf_cnpj`, `numero`, `cep`, `logradouro`, `documento`, `bairro`, `cidade`, `estado`, `complemento`, `perfil`, `motivo_recusa`, `email`, `senha_hash`, `telefone`, `situacao_cadastro`, `data_criacao`) VALUES
(1, 'roberto', '4934y67', '43798', '334345', 'gfndgn', NULL, 'jnhdfiufgvhn', 'kjdefhudfhui', 'PE', 'kjvndfjkvn', 'adotante', 'lkjvdfgvhifudhgudn', 'jfiubhudfhbnudbnho', 'teste', 'lkjdfjgiodfnvbd', 'ativo', '2026-10-31 12:04:58'),
(2, 'roberto', '11111111', '11111', '11111', '111111', NULL, '11111', '11111', 'RJ', NULL, 'adotante', NULL, 'a@gmail.com', '$2y$10$e2rvRCjB1z33qz0afaLg4Ojh0ldrbeCmNFcnyHu21bQGxqJZAOTau', '89489', 'ativo', '2026-11-05 20:39:42');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `doacoes`
--
ALTER TABLE `doacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_doador` (`doador_id`),
  ADD KEY `fk_recebedor` (`recebedor_id`);

--
-- Índices de tabela `fotos_gato`
--
ALTER TABLE `fotos_gato`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fotos_gato` (`gato_id`);

--
-- Índices de tabela `gatos`
--
ALTER TABLE `gatos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gatos_usuario` (`usuario_id`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notificacao_usuario` (`usuario_id`);

--
-- Índices de tabela `solicitacoes_adocao`
--
ALTER TABLE `solicitacoes_adocao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `adotante_id` (`adotante_id`,`gato_id`),
  ADD KEY `fk_gato` (`gato_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf_cnpj` (`cpf_cnpj`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `doacoes`
--
ALTER TABLE `doacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fotos_gato`
--
ALTER TABLE `fotos_gato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `gatos`
--
ALTER TABLE `gatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `solicitacoes_adocao`
--
ALTER TABLE `solicitacoes_adocao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `doacoes`
--
ALTER TABLE `doacoes`
  ADD CONSTRAINT `fk_doador` FOREIGN KEY (`doador_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_recebedor` FOREIGN KEY (`recebedor_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `fotos_gato`
--
ALTER TABLE `fotos_gato`
  ADD CONSTRAINT `fk_fotos_gato` FOREIGN KEY (`gato_id`) REFERENCES `gatos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `gatos`
--
ALTER TABLE `gatos`
  ADD CONSTRAINT `fk_gatos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `fk_notificacao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `solicitacoes_adocao`
--
ALTER TABLE `solicitacoes_adocao`
  ADD CONSTRAINT `fk_adotante` FOREIGN KEY (`adotante_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_gato` FOREIGN KEY (`gato_id`) REFERENCES `gatos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
