-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27/11/2026 às 12:00
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
-- Banco de dados: `insightcx-db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `analises_chatbot`
--

CREATE TABLE `analises_chatbot` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_feedback` bigint(20) UNSIGNED NOT NULL,
  `analise` text NOT NULL,
  `palavras_chave` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`palavras_chave`)),
  `prioridade_identificada` enum('baixa','media','alta') NOT NULL,
  `tipo_atendimento` enum('pendente','automatico','manual') NOT NULL DEFAULT 'pendente',
  `resposta_gerada` text DEFAULT NULL,
  `status_atendimento` enum('aguardando_analise','respondido_automaticamente','aguardando_administrador','em_atendimento','finalizado') NOT NULL DEFAULT 'aguardando_analise',
  `resposta_enviada_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `resposta_administrador` text DEFAULT NULL,
  `respondido_administrador_em` timestamp NULL DEFAULT NULL,
  `sentimento` enum('positivo','negativo','misto','neutro') DEFAULT NULL,
  `aspectos_positivos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`aspectos_positivos`)),
  `aspectos_negativos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`aspectos_negativos`)),
  `problemas_identificados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`problemas_identificados`)),
  `recomendacoes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recomendacoes`)),
  `confianca_analise` decimal(5,2) DEFAULT NULL,
  `resumo_gerencial` text DEFAULT NULL,
  `status_aprovacao` enum('aguardando','aprovada','recusada','nao_gerada') NOT NULL DEFAULT 'aguardando',
  `avaliado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `avaliado_em` timestamp NULL DEFAULT NULL,
  `motivo_recusa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `analises_chatbot`
--

INSERT INTO `analises_chatbot` (`id`, `id_feedback`, `analise`, `palavras_chave`, `prioridade_identificada`, `tipo_atendimento`, `resposta_gerada`, `status_atendimento`, `resposta_enviada_em`, `created_at`, `updated_at`, `resposta_administrador`, `respondido_administrador_em`, `sentimento`, `aspectos_positivos`, `aspectos_negativos`, `problemas_identificados`, `recomendacoes`, `confianca_analise`, `resumo_gerencial`, `status_aprovacao`, `avaliado_por`, `avaliado_em`, `motivo_recusa`) VALUES
(1, 2, 'O feedback não pôde ser classificado com segurança e precisa de avaliação humana.', '[]', 'media', 'manual', NULL, 'aguardando_administrador', NULL, '2026-11-13 21:11:46', '2026-11-13 21:11:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aguardando', NULL, NULL, NULL),
(2, 3, 'O feedback apresenta indícios de problemas no serviço e precisa de avaliação do administrador.', '[\"lentidao\",\"demora\",\"problema\"]', 'media', 'manual', NULL, 'aguardando_administrador', NULL, '2026-11-13 21:14:45', '2026-11-13 21:14:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aguardando', NULL, NULL, NULL),
(3, 4, 'O feedback apresenta indícios de uma falha crítica e precisa de avaliação do administrador.', '[\"sistema fora do ar\",\"problema\"]', 'alta', 'manual', NULL, 'finalizado', NULL, '2026-11-13 21:19:47', '2026-11-18 20:45:32', 'ok podemos fz algma coisa pra tentar evitar', '2026-11-18 20:45:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aguardando', NULL, NULL, NULL),
(4, 5, 'O feedback apresenta características positivas e pode receber uma resposta automática.', '[\"excelente\",\"gostei\",\"eficiente\"]', 'baixa', 'automatico', 'Agradecemos pelo seu feedback! Ficamos felizes em saber que sua experiência foi positiva. Sua opinião é muito importante para continuarmos melhorando nossos serviços.', 'respondido_automaticamente', '2026-11-25 15:53:19', '2026-11-18 20:07:13', '2026-11-25 15:53:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aprovada', 1, '2026-11-25 15:53:19', NULL),
(5, 7, 'O feedback não pôde ser classificado com segurança e precisa de avaliação humana.', '[]', 'media', 'manual', NULL, 'aguardando_administrador', NULL, '2026-11-25 14:46:12', '2026-11-25 14:46:12', NULL, NULL, NULL, '[]', '[]', '[]', '[]', NULL, 'Análise inteligente indisponível. É necessária avaliação manual.', 'aguardando', NULL, NULL, NULL),
(6, 8, 'O feedback apresenta indícios de problemas no serviço e precisa de avaliação do administrador.', '[\"demora\",\"problema\"]', 'media', 'manual', NULL, 'aguardando_administrador', NULL, '2026-11-25 14:50:04', '2026-11-25 14:50:04', NULL, NULL, NULL, '[]', '[]', '[]', '[]', NULL, 'Análise inteligente indisponível. É necessária avaliação manual.', 'aguardando', NULL, NULL, NULL),
(7, 9, 'O feedback apresenta indícios de problemas no serviço e precisa de avaliação do administrador.', '[\"problema\",\"satisfeito\"]', 'media', 'manual', NULL, 'aguardando_administrador', NULL, '2026-11-25 14:53:20', '2026-11-25 14:53:20', NULL, NULL, NULL, '[]', '[]', '[]', '[]', NULL, 'Análise inteligente indisponível. É necessária avaliação manual.', 'aguardando', NULL, NULL, NULL),
(9, 11, 'O feedback apresenta indícios de problemas no serviço e precisa de avaliação do administrador.', '[\"lentidao\",\"demora\"]', 'media', 'manual', NULL, 'finalizado', NULL, '2026-11-25 15:43:13', '2026-11-25 15:53:36', 'sfghghjgfdsasfdbgnm,jmhgfdf', '2026-11-25 15:53:36', NULL, '[]', '[]', '[]', '[]', NULL, 'Análise inteligente indisponível. É necessária avaliação manual.', 'nao_gerada', NULL, NULL, NULL),
(10, 12, 'O feedback apresenta indícios de problemas no serviço e precisa de avaliação do administrador.', '[\"demora\",\"problema\",\"eficiente\"]', 'media', 'manual', NULL, 'finalizado', NULL, '2026-11-25 15:51:34', '2026-11-25 15:53:27', 'alkjdfdnofldffdfdff', '2026-11-25 15:53:27', NULL, '[]', '[]', '[]', '[]', NULL, 'Análise inteligente indisponível. É necessária avaliação manual.', 'nao_gerada', NULL, NULL, NULL),
(11, 13, 'O feedback apresenta indícios de problemas no serviço e precisa de avaliação do administrador.', '[\"dificuldade\",\"excelente\",\"satisfeito\"]', 'media', 'manual', NULL, 'aguardando_administrador', NULL, '2026-11-25 15:59:05', '2026-11-25 15:59:05', NULL, NULL, NULL, '[]', '[]', '[]', '[]', NULL, 'Análise inteligente indisponível. É necessária avaliação manual.', 'nao_gerada', NULL, NULL, NULL),
(12, 14, 'O feedback apresenta indícios de problemas no serviço e precisa de avaliação do administrador.', '[\"demora\",\"erro\",\"dificuldade\",\"problema\"]', 'media', 'manual', NULL, 'aguardando_administrador', NULL, '2026-11-25 16:03:03', '2026-11-25 16:03:03', NULL, NULL, NULL, '[]', '[]', '[]', '[]', NULL, 'Análise inteligente indisponível. É necessária avaliação manual.', 'nao_gerada', NULL, NULL, NULL),
(13, 15, 'O feedback não pôde ser classificado com segurança e precisa de avaliação humana.', '[]', 'media', 'manual', NULL, 'finalizado', NULL, '2026-11-25 19:50:02', '2026-11-25 19:50:30', 'vixe', '2026-11-25 19:50:30', NULL, '[]', '[]', '[]', '[]', NULL, 'Análise inteligente indisponível. É necessária avaliação manual.', 'nao_gerada', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas`
--

CREATE TABLE `empresas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cnpj` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `empresas`
--

INSERT INTO `empresas` (`id`, `nome`, `cnpj`, `email`, `created_at`, `updated_at`) VALUES
(1, 'CTI', '12345678000190', 'cti@gmail.com', '2026-11-11 18:11:41', '2026-11-11 18:11:41'),
(2, 'Alfa Comercio', '98765432000110', 'alfa@gmail.com', '2026-11-11 18:11:41', '2026-11-11 18:11:41'),
(3, 'Google', '98765432563543', 'dhiogo@gmail.com', '2026-11-13 17:04:53', '2026-11-13 17:04:53'),
(4, 'Apple', '12345678978909', 'dhiego@gmail.com', '2026-11-25 19:15:36', '2026-11-25 19:15:36'),
(5, 'minha empresa', '99999999999999', 'minhaempresadoguilhermegomes@gmail.com', '2026-11-25 19:46:38', '2026-11-25 19:46:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `feedback`
--

CREATE TABLE `feedback` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `servico` varchar(255) DEFAULT NULL,
  `assunto` varchar(255) NOT NULL,
  `comentario` text NOT NULL,
  `categoria` enum('elogio','reclamacao') NOT NULL,
  `status` enum('pendente','em analise','resolvido') NOT NULL DEFAULT 'pendente',
  `prioridade` enum('baixa','media','alta') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `feedback`
--

INSERT INTO `feedback` (`id`, `id_usuario`, `servico`, `assunto`, `comentario`, `categoria`, `status`, `prioridade`, `created_at`, `updated_at`) VALUES
(1, 2, 'Suporte Técnico', 'adsfdsds', 'adshdgfgchsjklç~xssxsxsxsxsxsxsas', 'reclamacao', 'pendente', 'media', '2026-11-13 19:16:39', '2026-11-13 19:16:39'),
(2, 2, 'Suporte Técnico', 'Carregamento lerdo', 'llllllllllllllllllllllllllllllllllllllllllllllllllllll', 'reclamacao', 'pendente', 'media', '2026-11-13 21:11:46', '2026-11-13 21:11:46'),
(3, 2, 'Suporte Técnico', 'Sistema apresentando lentidão e falha', 'Estou enfrentando problemas constantes ao utilizar o sistema. As páginas estão demorando muito para carregar e, em alguns momentos, o sistema trava durante a utilização. Essa situação está prejudicando a realização das minhas atividades e causando atrasos no trabalho. Gostaria que o problema fosse analisado e resolvido o mais rápido possível.', 'reclamacao', 'pendente', 'media', '2026-11-13 21:14:45', '2026-11-13 21:14:45'),
(4, 2, 'Suporte Técnico', 'Sistema fora do ar', 'O sistema está completamente fora do ar e não conseguimos acessar nenhuma funcionalidade. Essa falha está impedindo a realização das nossas atividades e causando grandes prejuízos à empresa. Precisamos que o problema seja resolvido com urgência, pois estamos impossibilitados de trabalhar.', 'reclamacao', 'pendente', 'alta', '2026-11-13 21:19:47', '2026-11-13 21:19:47'),
(5, 2, 'Suporte Técnico', 'Atendimento excelente', 'O atendimento foi excelente! Gostei muito do serviço. A equipe foi eficiente e resolveu minha necessidade rapidamente.', 'elogio', 'pendente', 'baixa', '2026-11-18 20:07:13', '2026-11-18 20:07:13'),
(6, 2, 'Suporte Técnico', 'Sistema lento', 'o sistema esta mto lerdo', 'reclamacao', 'pendente', 'media', '2026-11-25 14:44:17', '2026-11-25 14:44:17'),
(7, 2, 'Suporte Técnico', 'Sistema lento', 'o sistema esta mto lerdo', 'reclamacao', 'pendente', 'media', '2026-11-25 14:46:09', '2026-11-25 14:46:09'),
(8, 2, 'Suporte Técnico', 'Demora no Atendimento e falta de organização', 'Gostaria de registrar minha insatisfação com o atendimento recebido. Ao chegar à empresa, precisei esperar aproximadamente 50 minutos para ser atendido, mesmo não havendo muitos clientes aguardando. Quando finalmente fui atendido, o funcionário demonstrou pouca organização e não conseguiu resolver meu problema na primeira tentativa. Precisei explicar a situação várias vezes e retornar ao atendimento para conseguir uma solução. Acredito que a empresa precisa melhorar a organização dos atendimentos, capacitar melhor seus funcionários e reduzir o tempo de espera, pois essa experiência foi bastante frustrante.', 'reclamacao', 'pendente', 'media', '2026-11-25 14:50:02', '2026-11-25 14:50:02'),
(9, 2, 'Suporte Técnico', 'Falhas graves no atendimento', 'Estou extremamente insatisfeito com o serviço prestado pela empresa. Já tentei resolver meu problema três vezes, mas em nenhuma das ocasiões consegui uma solução. Além disso, os funcionários forneceram informações diferentes, demonstrando falta de preparo e organização. Por causa dessa falha, perdi um compromisso importante e tive prejuízo financeiro. Entrei em contato novamente, mas continuo sem uma resposta concreta. A empresa precisa resolver essa situação com urgência, melhorar a comunicação entre os funcionários e garantir que problemas como esse não voltem a acontecer.', 'reclamacao', 'pendente', 'media', '2026-11-25 14:53:18', '2026-11-25 14:53:18'),
(10, 2, 'Suporte Técnico', 'Sistema fora do ar durante o expediente', 'Estou enfrentando problemas para acessar o sistema desde o início da manhã. Ao tentar realizar o login, a página fica carregando por muito tempo e, em seguida, apresenta uma mensagem de erro. Já tentei atualizar a página e acessar por outros navegadores, mas o problema continua. Essa situação está prejudicando minhas atividades e atrasando o atendimento aos clientes. Solicito que a equipe técnica verifique o problema e apresente uma solução o mais rápido possível.', 'reclamacao', 'pendente', 'alta', '2026-11-25 15:36:16', '2026-11-25 15:36:19'),
(11, 2, 'Suporte Técnico', 'Lentidão no atendimento', 'Gostaria de registrar uma reclamação sobre a demora no atendimento. Entrei em contato com a equipe de suporte para tirar uma dúvida sobre a utilização do sistema, mas precisei esperar bastante tempo até receber uma resposta. Quando fui atendido, consegui resolver minha dúvida, porém acredito que o tempo de espera poderia ser menor. Espero que a empresa consiga melhorar esse processo para oferecer uma experiência mais ágil aos clientes.', 'reclamacao', 'pendente', 'media', '2026-11-25 15:43:10', '2026-11-25 15:43:10'),
(12, 2, 'Suporte Técnico', 'Demora na resposta do suporte', 'Gostaria de registrar uma reclamação sobre a demora no atendimento. Entrei em contato com o suporte técnico para resolver um problema de acesso ao sistema, mas precisei esperar muito tempo para receber uma resposta. Além disso, não recebi orientações claras sobre como solucionar a situação. Espero que a equipe consiga melhorar o tempo de atendimento e oferecer um suporte mais eficiente aos clientes.', 'reclamacao', 'pendente', 'media', '2026-11-25 15:51:31', '2026-11-25 15:51:31'),
(13, 2, 'Suporte Técnico', 'Excelente atendimento da equipe', 'Gostaria de elogiar o atendimento recebido pela equipe de suporte. Tive dificuldades para utilizar algumas funcionalidades do sistema e entrei em contato para solicitar ajuda. Fui atendido rapidamente, recebi explicações claras e consegui resolver minhas dúvidas. A equipe demonstrou atenção, paciência e profissionalismo durante todo o atendimento. Estou muito satisfeito com a qualidade do serviço.', 'elogio', 'pendente', 'media', '2026-11-25 15:59:03', '2026-11-25 15:59:03'),
(14, 2, 'Suporte Técnico', 'Erro ao acessar funcionalidades do sistema', 'Estou enfrentando dificuldades para acessar algumas funcionalidades do sistema. Ao tentar utilizar os recursos disponíveis, algumas páginas demoram para carregar e, em determinados momentos, apresentam mensagens de erro. Já tentei atualizar a página e realizar o acesso novamente, mas o problema continua acontecendo. Gostaria de receber orientações para solucionar essa situação e espero que a equipe possa verificar o que está causando essas falhas.', 'reclamacao', 'pendente', 'media', '2026-11-25 16:03:01', '2026-11-25 16:03:01'),
(15, 2, 'Suporte Técnico', 'ligo e não atende', 'liguei e não atenderam', 'reclamacao', 'pendente', 'media', '2026-11-25 19:49:56', '2026-11-25 19:49:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_11_11_100000_create_empresas_table', 1),
(5, '2026_11_11_102210_create_personal_access_tokens_table', 1),
(6, '2026_11_11_104252_create_usuarios_table', 1),
(7, '2026_11_11_120423_create_servicos_table', 1),
(8, '2026_11_11_123000_create_feedback_table', 1),
(9, '2026_11_13_134757_adicionar_cpf_tabela_usuarios', 2),
(10, '2026_11_13_162500_create_sugestaos_table', 2),
(11, '2026_11_13_172135_adicionar_campos_chatbot_na_tabela_feedback', 2),
(12, '2026_11_13_175715_remover_campos_chatbot_da_tabela_feedback', 3),
(13, '2026_11_13_175940_create_analise_chatbots_table', 4),
(14, '2026_11_18_163448_adicionar_resposta_administrador_em_analises_chatbot', 5),
(15, '2026_11_25_111737_adicionar_dados_analise_ia_em_analises_chatbot', 6),
(16, '2026_11_25_120022_adicionar_fluxo_aprovacao_ia_em_analises_chatbot', 7),
(17, '2026_11_25_171932_substituir_id_servico_por_servico_em_feedback', 8);

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Usuario', 1, 'auth_token', '0519c075e7b623f3e44678ae89e349bdd10c0c07588f7f7f5594595893b3c0ca', '[\"*\"]', NULL, NULL, '2026-11-11 21:25:07', '2026-11-11 21:25:07'),
(2, 'App\\Models\\Usuario', 1, 'auth_token', '6a7a79d6e734c74b30551ba37e0e5862a64be40cb1b3ef66e0383aa2a2adcf9d', '[\"*\"]', '2026-11-11 21:30:55', NULL, '2026-11-11 21:27:58', '2026-11-11 21:30:55'),
(3, 'App\\Models\\Usuario', 1, 'auth_token', 'a0d9503cbfcf11e77ba1a3ebfef8fae58a1b7e33fdf4047302e3b12f68e402ed', '[\"*\"]', '2026-11-11 21:35:17', NULL, '2026-11-11 21:33:47', '2026-11-11 21:35:17'),
(4, 'App\\Models\\Usuario', 1, 'auth_token', '4b29bd24a993fb90518238e3fecff382723fa3f8da35915bea8fc89c61e60fad', '[\"*\"]', NULL, NULL, '2026-11-13 13:30:52', '2026-11-13 13:30:52'),
(5, 'App\\Models\\Usuario', 1, 'auth_token', '4664b532de762df4d998c16dd7e9e452378adac3f773b8ed8e90351ea09406f7', '[\"*\"]', NULL, NULL, '2026-11-13 13:33:23', '2026-11-13 13:33:23'),
(6, 'App\\Models\\Usuario', 2, 'auth_token', 'ee0a295e8324a04161a2d44c41215774282acd9fde9527a59e09db1b039b75a8', '[\"*\"]', NULL, NULL, '2026-11-13 13:34:14', '2026-11-13 13:34:14'),
(7, 'App\\Models\\Usuario', 2, 'auth_token', 'de9bd311c7e1562127f10a2d5798e775884585b2c2483f65f988bcf93581e97f', '[\"*\"]', NULL, NULL, '2026-11-13 13:36:03', '2026-11-13 13:36:03'),
(8, 'App\\Models\\Usuario', 2, 'auth_token', '60c99be9ae9a1c84989372642ce318ae53e63ba80988cb0918249e31ef75b47c', '[\"*\"]', NULL, NULL, '2026-11-13 13:36:15', '2026-11-13 13:36:15'),
(9, 'App\\Models\\Usuario', 2, 'auth_token', '88ef58ff6e7ce9533f9efa6aea7ccef5671863dbb3481d29cd95295e96eccfba', '[\"*\"]', '2026-11-13 21:26:59', NULL, '2026-11-13 13:40:23', '2026-11-13 21:26:59'),
(10, 'App\\Models\\Usuario', 1, 'auth_token', '05af6f204f98a8a06314377efd893e0911020b163a8b312d9d9b345a2afbc024', '[\"*\"]', NULL, NULL, '2026-11-13 15:58:07', '2026-11-13 15:58:07'),
(12, 'App\\Models\\Usuario', 2, 'auth_token', '53e7fd5e9fc9f12eff1905ffc194132e8809747a63615c86dedd4926d2477865', '[\"*\"]', NULL, NULL, '2026-11-13 16:03:46', '2026-11-13 16:03:46'),
(13, 'App\\Models\\Usuario', 3, 'auth_token', '3c16e562928b3ee3d39108f3c189c687a380ea0ff756eab4a12a80ae54bb06ef', '[\"*\"]', NULL, NULL, '2026-11-13 17:05:10', '2026-11-13 17:05:10'),
(14, 'App\\Models\\Usuario', 3, 'auth_token', '78a0a3e113736e25260ce63359a3e431f3a6778caccb363ad11dd5b4a9497aaa', '[\"*\"]', NULL, NULL, '2026-11-13 17:05:55', '2026-11-13 17:05:55'),
(15, 'App\\Models\\Usuario', 3, 'auth_token', '83218f270ccaf2e9430738c73af668ce910cb1f4a60459105e392004f50828a7', '[\"*\"]', NULL, NULL, '2026-11-13 17:08:45', '2026-11-13 17:08:45'),
(16, 'App\\Models\\Usuario', 3, 'auth_token', 'd025c1e3fa3d8ee40accb8807cfe025329536f64ae0177734ceda20eb753e8fe', '[\"*\"]', '2026-11-13 19:14:23', NULL, '2026-11-13 17:45:46', '2026-11-13 19:14:23'),
(17, 'App\\Models\\Usuario', 3, 'auth_token', '63d3d4083b4aff3c0b39df0ab216f3e177bb65a80b8133adce66eb83a1fe9ab4', '[\"*\"]', '2026-11-13 19:13:26', NULL, '2026-11-13 19:13:21', '2026-11-13 19:13:26'),
(18, 'App\\Models\\Usuario', 2, 'auth_token', '205a0a86252dd7592fe305644600adb448c3e56149d5a24b3f92e61734a8fad4', '[\"*\"]', '2026-11-13 19:37:37', NULL, '2026-11-13 19:16:25', '2026-11-13 19:37:37'),
(20, 'App\\Models\\Usuario', 3, 'auth_token', 'c0ebc913bc5d6a8a2db9c6b44930e2b8806a488ad71d6ede2ab9997b09aace98', '[\"*\"]', '2026-11-13 21:10:47', NULL, '2026-11-13 21:10:38', '2026-11-13 21:10:47'),
(21, 'App\\Models\\Usuario', 2, 'auth_token', '5a7f1f0c992fca6a9d02f5c6e8cb51c0d3f8ee9d28de41c11b084d18a575b998', '[\"*\"]', '2026-11-13 21:34:05', NULL, '2026-11-13 21:11:14', '2026-11-13 21:34:05'),
(22, 'App\\Models\\Usuario', 3, 'auth_token', '6aa10bfe7977e4e0065ef2d0569753eecf47e699c870da1dca3557c68af62776', '[\"*\"]', '2026-11-18 19:25:41', NULL, '2026-11-18 19:20:57', '2026-11-18 19:25:41'),
(24, 'App\\Models\\Usuario', 3, 'auth_token', 'b0b506e2e61a2bce4f985fb577a63fd14052deaac2594c3180b75ed011615fb1', '[\"*\"]', '2026-11-18 20:06:03', NULL, '2026-11-18 20:06:01', '2026-11-18 20:06:03'),
(25, 'App\\Models\\Usuario', 2, 'auth_token', 'cc7e4c1b2334290f48857b637059cfe2ac1ab60ad3a8089e7c8ec1f52d42051e', '[\"*\"]', '2026-11-18 20:59:56', NULL, '2026-11-18 20:06:34', '2026-11-18 20:59:56'),
(26, 'App\\Models\\Usuario', 1, 'auth_token', 'f66bb970ab8a9d0b0964a4843bf6830ca19b55f45f4351908652619a22012796', '[\"*\"]', '2026-11-18 20:45:33', NULL, '2026-11-18 20:45:01', '2026-11-18 20:45:33'),
(27, 'App\\Models\\Usuario', 3, 'auth_token', 'd3511ad3869ad4f80d50e5725347d2830c246aebe9c9ef435b2c07b8fe091131', '[\"*\"]', '2026-11-25 13:23:10', NULL, '2026-11-25 13:22:45', '2026-11-25 13:23:10'),
(29, 'App\\Models\\Usuario', 3, 'auth_token', 'f141168d0396e766f3a6a71b31a48c27726f53db0a5928aed8bc44df0666a209', '[\"*\"]', '2026-11-25 13:25:53', NULL, '2026-11-25 13:25:36', '2026-11-25 13:25:53'),
(30, 'App\\Models\\Usuario', 3, 'auth_token', '168054b650bd56ab0a88b5dd3e657eb48779cbdb116c7d6ddec74c8a64c79cf5', '[\"*\"]', '2026-11-25 13:53:50', NULL, '2026-11-25 13:53:18', '2026-11-25 13:53:50'),
(31, 'App\\Models\\Usuario', 2, 'auth_token', '6d4d4673075e7ab6687b858c815a37dcf26386a229a61369d90e14600ac394f9', '[\"*\"]', '2026-11-25 13:57:24', NULL, '2026-11-25 13:54:41', '2026-11-25 13:57:24'),
(32, 'App\\Models\\Usuario', 2, 'auth_token', 'd110ef1c5395c8766cef5383e752fc75387c30dd053262f9fe40e3a44dd19e56', '[\"*\"]', '2026-11-25 19:14:51', NULL, '2026-11-25 14:43:39', '2026-11-25 19:14:51'),
(34, 'App\\Models\\Usuario', 1, 'auth_token', '99c3018827a35494a8621debfbf2583dadbe12a16d0f156ac79e4fa0a351fde5', '[\"*\"]', '2026-11-25 19:51:37', NULL, '2026-11-25 15:35:23', '2026-11-25 19:51:37'),
(35, 'App\\Models\\Usuario', 4, 'auth_token', 'c6357b8f023b2f00379d98cf8a4ee2f0b83271529e7284425189c323d855c93a', '[\"*\"]', '2026-11-25 19:23:29', NULL, '2026-11-25 19:15:54', '2026-11-25 19:23:29'),
(36, 'App\\Models\\Usuario', 5, 'auth_token', 'c4d67f36b569c277006f24d5a6fe734b38952dd6d6bed06ba93e3899c187ebd2', '[\"*\"]', '2026-11-25 19:48:11', NULL, '2026-11-25 19:47:02', '2026-11-25 19:48:11'),
(37, 'App\\Models\\Usuario', 2, 'auth_token', '69bdd3087a48c22ee98153c2c05f1149571480a709bce346235c422c44e1a57d', '[\"*\"]', '2026-11-25 19:50:45', NULL, '2026-11-25 19:48:53', '2026-11-25 19:50:45');

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `id_empresa` bigint(20) UNSIGNED NOT NULL,
  `descricao` text NOT NULL,
  `status` enum('ativo','inativo') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`id`, `nome`, `id_empresa`, `descricao`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Atendimento ao Cliente', 1, 'Serviço de atendimento e relacionamento com clientes.', 'ativo', '2026-11-13 10:47:41', '2026-11-13 10:47:41'),
(2, 'Suporte Técnico', 2, 'Serviço de suporte técnico e resolução de problemas.', 'ativo', '2026-11-13 10:47:41', '2026-11-13 10:47:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('pNI2hyd5mRKSYRc495GS00Mz8qt6SQjYyMZ9DdhY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiSHk5djRKYXpaV2VWNTRoamZNNlNTbktZSWNFV1lSbXhLakRJNnMwNCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1794568505);

-- --------------------------------------------------------

--
-- Estrutura para tabela `sugestoes`
--

CREATE TABLE `sugestoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_empresa` bigint(20) UNSIGNED NOT NULL,
  `id_servico` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `status` enum('pendente','em andamento','concluida') NOT NULL DEFAULT 'pendente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(255) NOT NULL,
  `tipo` enum('admin','cliente') NOT NULL,
  `id_empresa` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `cpf`, `senha`, `telefone`, `tipo`, `id_empresa`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@gmail.com', '', '$2y$12$LqX5VMtB3/JvBgd7sZNC0u53giLlaVbqhzcQxUqKY10PeUXhp6Qh6', '14999990001', 'admin', 1, '2026-11-11 18:21:28', '2026-11-13 10:32:55'),
(2, 'Cliente Alfa', 'cliente@alfa.com', '', '$2y$12$LqX5VMtB3/JvBgd7sZNC0u53giLlaVbqhzcQxUqKY10PeUXhp6Qh6', '14999990002', 'cliente', 2, '2026-11-11 18:21:28', '2026-11-13 10:32:55'),
(3, 'Dhiogo Antonio Marestoni dos Santos', 'dhiogo@gmail.com', '92876352273', '$2y$12$ltif/vnjSzLSLdlBRsZLbeudFM5AvOjNwza44uBPWcH4qXK8MfkDu', '1899996666', 'cliente', 3, '2026-11-13 17:04:53', '2026-11-13 17:04:53'),
(4, 'dhiego', 'dhiego@gmail.com', '12345676543', '$2y$12$kUeULj.ixExHIkAZy3XsUeQKO4tByxMkUeGijRSx7Irhk9qQkvMZy', '23456765432', 'cliente', 4, '2026-11-25 19:15:36', '2026-11-25 19:15:36'),
(5, 'guilherme gomes do nascimento', 'minhaempresadoguilhermegomes@gmail.com', '99999999999', '$2y$12$RRnn2jD6.RXEVeD1vlvIfeD05OEW98sVSq5SDloN0UKIJctVBOm1S', '18994444444', 'cliente', 5, '2026-11-25 19:46:38', '2026-11-25 19:46:38');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `analises_chatbot`
--
ALTER TABLE `analises_chatbot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analises_chatbot_id_feedback_foreign` (`id_feedback`),
  ADD KEY `analises_chatbot_avaliado_por_foreign` (`avaliado_por`);

--
-- Índices de tabela `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Índices de tabela `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Índices de tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `empresas_cnpj_unique` (`cnpj`),
  ADD UNIQUE KEY `empresas_email_unique` (`email`);

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices de tabela `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_id_usuario_foreign` (`id_usuario`);

--
-- Índices de tabela `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Índices de tabela `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Índices de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servicos_id_empresa_foreign` (`id_empresa`);

--
-- Índices de tabela `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Índices de tabela `sugestoes`
--
ALTER TABLE `sugestoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sugestoes_id_empresa_foreign` (`id_empresa`),
  ADD KEY `sugestoes_id_servico_foreign` (`id_servico`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuarios_email_unique` (`email`),
  ADD KEY `usuarios_id_empresa_foreign` (`id_empresa`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `analises_chatbot`
--
ALTER TABLE `analises_chatbot`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `sugestoes`
--
ALTER TABLE `sugestoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `analises_chatbot`
--
ALTER TABLE `analises_chatbot`
  ADD CONSTRAINT `analises_chatbot_avaliado_por_foreign` FOREIGN KEY (`avaliado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `analises_chatbot_id_feedback_foreign` FOREIGN KEY (`id_feedback`) REFERENCES `feedback` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `servicos`
--
ALTER TABLE `servicos`
  ADD CONSTRAINT `servicos_id_empresa_foreign` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `sugestoes`
--
ALTER TABLE `sugestoes`
  ADD CONSTRAINT `sugestoes_id_empresa_foreign` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sugestoes_id_servico_foreign` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_id_empresa_foreign` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
