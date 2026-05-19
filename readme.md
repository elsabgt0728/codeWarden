codewarden/
│
├── app/
│   │
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── CandidatController.php
│   │   ├── AdminController.php
│   │   ├── JeuController.php
│   │   ├── TestController.php
│   │   └── ScoreController.php
│   │
│   ├── models/
│   │   ├── Candidat.php
│   │   ├── Admin.php
│   │   ├── JeuLogique.php  
│   │   ├── Test.php
│   │   ├── Resultat.php
│   │   └── Session.php
│   │
│   ├── views/
│   │   ├── auth/
│   │   ├── candidat/
│   │   ├── admin/
│   │   └── jeux/
│   │
│   ├── services/
│   │   ├── AuthService.php
│   │   ├── ScoreService.php
│   │   ├── GameEngineService.php
│   │   └── StatisticsService.php
│   │
│   ├── middlewares/
│   │   ├── AuthMiddleware.php
│   │   └── AdminMiddleware.php
│   │
│   └── core/
│       ├── Database.php
│       ├── Router.php
│       ├── Controller.php
│       └── Model.php
│
├── public/
│   ├── index.php
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
│
├── routes/
│   └── web.php
│
├── config/
│   └── config.php
│
├── database/
│   └── codewarden.sql
│
└── .htaccess