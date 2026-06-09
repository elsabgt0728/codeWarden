<?php
session_start();

const ROOT     = __DIR__;
const BASE_URL = '/codewarden'; // Modifier si votre projet est dans un autre dossier

require_once ROOT . '/app/core/database.php';
require_once ROOT . '/app/core/helpers.php';
require_once ROOT . '/app/models/candidat.php';
require_once ROOT . '/app/models/admin.php';
require_once ROOT . '/app/middlewares/auth.php';
require_once ROOT . '/app/controllers/auth_controller.php';
require_once ROOT . '/app/controllers/candidat_controller.php';
require_once ROOT . '/app/controllers/admin_controller.php';

require ROOT . '/routes/web.php';
