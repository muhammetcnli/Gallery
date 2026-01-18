# Gallery
Gallery app for Politechnika Gdanska

## MVC structure (rubric 1.0)

- Front controller + routing: [public/index.php](public/index.php)
- Controllers (request handling + response/redirect): [src/controllers.php](src/controllers.php)
- Business logic helpers (separated functions): [src/services.php](src/services.php)
- Model / DB layer (MongoDB helpers): [config/db.php](config/db.php)
- Views (presentation): [views/](views/)

### Main flows to demo

- Register/Login/Logout: routes in [public/index.php](public/index.php), actions in [src/controllers.php](src/controllers.php)
- Upload + thumbnail + watermark: action in [src/controllers.php](src/controllers.php), image helpers in [src/services.php](src/services.php)
- Gallery + pagination + visibility filter: action in [src/controllers.php](src/controllers.php), filter in [src/services.php](src/services.php)
- AJAX search: view JS in [views/search_view.php](views/search_view.php), endpoint in [src/controllers.php](src/controllers.php)
