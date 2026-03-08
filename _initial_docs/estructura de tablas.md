\# Plataforma de Capacitación Empresarial — Esquema de Base de Datos (Laravel + MySQL)



> Fuente: estructura de tablas proporcionada por el cliente. :contentReference\[oaicite:0]{index=0}



---



\## 1) RBAC + Organización



\### Tabla: `users`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id` (bigint, PK)

\- `name` (string)

\- `email` (string, unique)

\- `password` (string)

\- `status` (enum: active | inactive | suspended)

\- `primary\_branch\_id` (bigint, FK → `branches.id`, nullable)

\- `department\_id` (bigint, FK → `departments.id`, nullable)

\- `position` (string, nullable)

\- `created\_by` (bigint, FK → `users.id`, nullable)

\- `created\_at`, `updated\_at`



\*\*Relaciones:\*\*

\- `users.department\_id` → `departments.id`

\- `users.primary\_branch\_id` → `branches.id`

\- N:N con `roles` vía `role\_user`

\- N:N con `branches` vía `branch\_user` (para jefes de zona)



---



\### Tabla: `roles`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `name` (string, unique) \*(ej: super\_admin, admin, user, manager)\*

\- `description` (string, nullable)

\- `created\_at`, `updated\_at`



---



\### Tabla: `permissions`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `name` (string, unique) \*(ej: admins.create, admins.delete, courses.assign, scorecard.edit)\*

\- `group` (string, nullable)

\- `created\_at`, `updated\_at`



---



\### Tabla pivote: `role\_user`

\*\*PK sugerida:\*\* compuesta (`role\_id`, `user\_id`)  

\*\*Campos:\*\*

\- `role\_id` (FK → `roles.id`)

\- `user\_id` (FK → `users.id`)



---



\### Tabla pivote: `permission\_role`

\*\*PK sugerida:\*\* compuesta (`permission\_id`, `role\_id`)  

\*\*Campos:\*\*

\- `permission\_id` (FK → `permissions.id`)

\- `role\_id` (FK → `roles.id`)



---



\### (Opcional) Tabla pivote: `permission\_user`

\*\*PK sugerida:\*\* compuesta (`permission\_id`, `user\_id`)  

\*\*Campos:\*\*

\- `permission\_id` (FK → `permissions.id`)

\- `user\_id` (FK → `users.id`)



---



\### Tabla: `departments`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `name` (string, unique)

\- `description` (string, nullable)

\- `active` (bool)

\- `created\_at`, `updated\_at`



---



\## 2) Multisucursal



\### Tabla: `branches`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `name` (string)

\- `code` (string, nullable)

\- `phone` (string, nullable)

\- `email` (string, nullable)



\*\*Dirección:\*\*

\- `country`, `state`, `city`, `zip` (string, nullable)

\- `address\_line1` (string, nullable)

\- `address\_line2` (string, nullable)

\- `lat`, `lng` (decimal, nullable)



\*\*Datos fiscales (opcionales):\*\*

\- `legal\_name` (string, nullable)

\- `tax\_id` (string, nullable)

\- `tax\_regime` (string, nullable)

\- `fiscal\_address\_\*` (campos que definas, nullable)

\- `invoice\_email` (string, nullable)



\- `active` (bool)

\- `created\_at`, `updated\_at`



---



\### Tabla pivote: `branch\_user`

\*\*PK sugerida:\*\* compuesta (`branch\_id`, `user\_id`)  

\*\*Campos:\*\*

\- `branch\_id` (FK → `branches.id`)

\- `user\_id` (FK → `users.id`)

\- `relation\_type` (enum: assigned | in\_charge)

\- `is\_primary` (bool)



---



\## 3) Cursos + Lecciones + Contenidos



\### Tabla: `course\_categories`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `name` (string, unique)

\- `description` (string, nullable)

\- `active` (bool)

\- `created\_at`, `updated\_at`



---



\### Tabla: `courses`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `course\_category\_id` (FK → `course\_categories.id`)

\- `title` (string)

\- `slug` (string, unique)

\- `description` (text)

\- `cover\_image\_path` (string, nullable)

\- `status` (enum: draft | published | archived)

\- `created\_by` (FK → `users.id`)

\- `created\_at`, `updated\_at`



---



\### Tabla: `lessons`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `course\_id` (FK → `courses.id`)

\- `title` (string)

\- `description` (text, nullable)

\- `order` (int)

\- `content\_type` (enum: youtube | pdf | image | ppt | file | html | external\_link)

\- `duration\_seconds` (int, nullable)

\- `is\_required` (bool, default true)

\- `created\_by` (FK → `users.id`)

\- `created\_at`, `updated\_at`



---



\### Tabla: `lesson\_contents`

\*(Para múltiples recursos por lección)\*  

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `lesson\_id` (FK → `lessons.id`)

\- `type` (enum: youtube | file | link)

\- `title` (string, nullable)

\- `url` (string, nullable)

\- `file\_path` (string, nullable)

\- `mime\_type` (string, nullable)

\- `size\_bytes` (bigint, nullable)

\- `meta\_json` (json, nullable)

\- `order` (int)

\- `created\_at`, `updated\_at`



---



\## 4) Asignación de cursos (por lote e individual)



\### Tabla: `course\_assignments`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `course\_id` (FK → `courses.id`)

\- `assigned\_by` (FK → `users.id`)

\- `assignment\_type` (enum: department | user | branch)

\- `department\_id` (FK → `departments.id`, nullable)

\- `user\_id` (FK → `users.id`, nullable)

\- `branch\_id` (FK → `branches.id`, nullable)

\- `assigned\_at` (datetime)

\- `due\_at` (datetime, nullable)

\- `notes` (string, nullable)

\- `created\_at`, `updated\_at`



---



\### Tabla pivote efectiva: `course\_user`

\*(Lista final de usuarios asignados a un curso; permite agregar/quitar manualmente)\*  

\*\*PK sugerida:\*\* compuesta (`course\_id`, `user\_id`)  

\*\*Campos:\*\*

\- `course\_id` (FK → `courses.id`)

\- `user\_id` (FK → `users.id`)

\- `assigned\_source` (enum: manual | department | branch)

\- `source\_id` (bigint, nullable)

\- `assigned\_by` (FK → `users.id`, nullable)

\- `assigned\_at` (datetime)

\- `due\_at` (datetime, nullable)

\- `status` (enum: not\_started | in\_progress | completed)

\- `revoked\_at` (datetime, nullable)

\- `revoked\_by` (FK → `users.id`, nullable)

\- `revoke\_reason` (string, nullable)

\- `created\_at`, `updated\_at`



---



\## 5) Progreso (lección 90% video) + Progreso de curso



\### Tabla: `lesson\_progress`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `course\_id` (FK → `courses.id`)

\- `lesson\_id` (FK → `lessons.id`)

\- `user\_id` (FK → `users.id`)

\- `started\_at` (datetime, nullable)

\- `completed\_at` (datetime, nullable)

\- `completion\_method` (enum: video\_90 | manual | document\_open | admin\_override)

\- `created\_at`, `updated\_at`



---



\### Tabla: `lesson\_video\_progress`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `lesson\_id` (FK → `lessons.id`)

\- `user\_id` (FK → `users.id`)

\- `provider` (enum: youtube)

\- `watched\_seconds` (int, default 0)

\- `last\_position\_seconds` (int, default 0)

\- `duration\_seconds` (int, nullable)

\- `percent\_watched` (decimal(5,2), nullable)

\- `last\_event\_at` (datetime)

\- `created\_at`, `updated\_at`



---



\### Tabla: `course\_progress`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `course\_id` (FK → `courses.id`)

\- `user\_id` (FK → `users.id`)

\- `percent\_completed` (decimal(5,2))

\- `lessons\_completed` (int)

\- `lessons\_total` (int)

\- `status` (enum: not\_started | in\_progress | completed)

\- `completed\_at` (datetime, nullable)

\- `last\_activity\_at` (datetime, nullable)

\- `created\_at`, `updated\_at`



---



\## 6) Quizzes / Exámenes



\### Tabla: `assessments`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `course\_id` (FK → `courses.id`)

\- `title` (string)

\- `type` (enum: quiz | exam)

\- `min\_score` (decimal(5,2), default 70)

\- `attempts\_allowed` (int, nullable)

\- `is\_required` (bool, default true)

\- `unlock\_rule` (enum: course\_100\_lessons)

\- `order` (int)

\- `created\_at`, `updated\_at`



---



\### Tabla: `assessment\_questions`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `assessment\_id` (FK → `assessments.id`)

\- `type` (enum: single\_choice | multi\_choice | true\_false | open)

\- `question\_text` (text)

\- `points` (decimal(6,2))

\- `order` (int)

\- `meta\_json` (json, nullable)

\- `created\_at`, `updated\_at`



---



\### Tabla: `assessment\_options`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `question\_id` (FK → `assessment\_questions.id`)

\- `option\_text` (text)

\- `is\_correct` (bool)

\- `order` (int)

\- `created\_at`, `updated\_at`



---



\### Tabla: `assessment\_attempts`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `assessment\_id` (FK → `assessments.id`)

\- `course\_id` (FK → `courses.id`)

\- `user\_id` (FK → `users.id`)

\- `status` (enum: in\_progress | submitted | graded)

\- `started\_at` (datetime)

\- `submitted\_at` (datetime, nullable)

\- `score` (decimal(6,2), nullable)

\- `passed` (bool, nullable)

\- `graded\_by` (FK → `users.id`, nullable)

\- `created\_at`, `updated\_at`



---



\### Tabla: `assessment\_answers`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `attempt\_id` (FK → `assessment\_attempts.id`)

\- `question\_id` (FK → `assessment\_questions.id`)

\- `selected\_option\_id` (FK → `assessment\_options.id`, nullable)

\- `answer\_text` (text, nullable)

\- `is\_correct` (bool, nullable)

\- `points\_awarded` (decimal(6,2), nullable)

\- `created\_at`, `updated\_at`



---



\## 7) Eventos / Analítica



\### Tabla: `activity\_events`

\*\*PK:\*\* `id`  

\*\*Campos:\*\*

\- `id`

\- `user\_id` (FK → `users.id`)

\- `event\_type` (enum: course\_assigned | course\_started | course\_completed | lesson\_started | lesson\_completed | video\_progress | assessment\_started | assessment\_submitted | assessment\_graded)

\- `course\_id` (FK → `courses.id`, nullable)

\- `lesson\_id` (FK → `lessons.id`, nullable)

\- `assessment\_id` (FK → `assessments.id`, nullable)

\- `attempt\_id` (FK → `assessment\_attempts.id`, nullable)

\- `branch\_id` (FK → `branches.id`, nullable) \*(snapshot)\*

\- `department\_id` (FK → `departments.id`, nullable) \*(snapshot)\*

\- `meta\_json` (json)

\- `created\_at` (datetime)



---



\## 8) Biblioteca



\### Tabla: `library\_categories`

\- `id` (PK)

\- `name` (unique)

\- `description` (nullable)

\- `active` (bool)

\- timestamps



\### Tabla: `resource\_types`

\- `id` (PK)

\- `name` (unique)

\- timestamps



\### Tabla: `library\_resources`

\- `id` (PK)

\- `library\_category\_id` (FK → `library\_categories.id`)

\- `resource\_type\_id` (FK → `resource\_types.id`)

\- `title`

\- `description` (nullable)

\- `content\_type` (enum: file | youtube | link)

\- `url` (nullable)

\- `file\_path` (nullable)

\- `mime\_type` (nullable)

\- `published\_at` (nullable)

\- `created\_by` (FK → `users.id`)

\- `active` (bool)

\- timestamps



---



\## 9) Avisos



\### Tabla: `announcements`

\- `id` (PK)

\- `title`

\- `body` (text)

\- `type` (enum: info | warning | promo, nullable)

\- `image\_path` (nullable)

\- `starts\_at`

\- `ends\_at` (nullable)

\- `created\_by` (FK → `users.id`)

\- `active` (bool)

\- timestamps



\### (Opcional) Tabla: `announcement\_targets`

\- `announcement\_id` (FK → `announcements.id`)

\- `target\_type` (enum: all | branch | department | role | user)

\- `target\_id` (nullable)



---



\## 10) Scorecard (configurable)



\### Tabla: `scorecard\_templates`

\- `id` (PK)

\- `name`

\- `description` (nullable)

\- `active` (bool)

\- `created\_by` (FK → `users.id`)

\- timestamps



\### Tabla: `scorecard\_fields`

\- `id` (PK)

\- `template\_id` (FK → `scorecard\_templates.id`)

\- `key` (string)

\- `label` (string)

\- `type` (enum: int | decimal | text | bool)

\- `required` (bool)

\- `order` (int)

\- `meta\_json` (json, nullable)

\- timestamps



\### Tabla: `scorecard\_entries`

\- `id` (PK)

\- `template\_id` (FK → `scorecard\_templates.id`)

\- `branch\_id` (FK → `branches.id`)

\- `user\_id` (FK → `users.id`)

\- `entry\_date` (date)

\- `status` (enum: draft | submitted | approved)

\- `approved\_by` (FK → `users.id`, nullable)

\- timestamps



\### Tabla: `scorecard\_values`

\- `id` (PK)

\- `entry\_id` (FK → `scorecard\_entries.id`)

\- `field\_id` (FK → `scorecard\_fields.id`)

\- `value\_int` (nullable)

\- `value\_decimal` (nullable)

\- `value\_text` (nullable)

\- `value\_bool` (nullable)

\- timestamps



---



\## 11) Índices recomendados



\- `course\_user`: UNIQUE(`course\_id`, `user\_id`)

\- `lesson\_progress`: UNIQUE(`lesson\_id`, `user\_id`)

\- `lesson\_video\_progress`: UNIQUE(`lesson\_id`, `user\_id`)

\- `course\_progress`: UNIQUE(`course\_id`, `user\_id`)

\- `assessment\_attempts`: INDEX(`assessment\_id`, `user\_id`, `started\_at`)

\- `activity\_events`: INDEX(`event\_type`, `created\_at`), INDEX(`branch\_id`, `created\_at`), INDEX(`course\_id`, `created\_at`)

\- `scorecard\_entries`: UNIQUE(`template\_id`, `branch\_id`, `entry\_date`)

\- `users`: INDEX(`department\_id`), INDEX(`primary\_branch\_id`)



---



\## 12) Diagrama ER (Mermaid)



```mermaid

erDiagram

&nbsp; USERS }o--|| DEPARTMENTS : belongs\_to

&nbsp; USERS }o--|| BRANCHES : primary\_branch

&nbsp; USERS }o--o{ BRANCH\_USER : multi\_branch

&nbsp; BRANCHES ||--o{ BRANCH\_USER : has



&nbsp; USERS }o--o{ ROLE\_USER : has

&nbsp; ROLES }o--o{ ROLE\_USER : has



&nbsp; ROLES }o--o{ PERMISSION\_ROLE : has

&nbsp; PERMISSIONS }o--o{ PERMISSION\_ROLE : has



&nbsp; COURSE\_CATEGORIES ||--o{ COURSES : has

&nbsp; COURSES ||--o{ LESSONS : has

&nbsp; LESSONS ||--o{ LESSON\_CONTENTS : has



&nbsp; COURSES }o--o{ COURSE\_USER : assigned

&nbsp; USERS }o--o{ COURSE\_USER : assigned



&nbsp; LESSONS ||--o{ LESSON\_PROGRESS : tracked

&nbsp; USERS ||--o{ LESSON\_PROGRESS : tracked

&nbsp; LESSONS ||--o{ LESSON\_VIDEO\_PROGRESS : tracked

&nbsp; USERS ||--o{ LESSON\_VIDEO\_PROGRESS : tracked



&nbsp; COURSES ||--o{ COURSE\_PROGRESS : tracked

&nbsp; USERS ||--o{ COURSE\_PROGRESS : tracked



&nbsp; COURSES ||--o{ ASSESSMENTS : has

&nbsp; ASSESSMENTS ||--o{ ASSESSMENT\_QUESTIONS : has

&nbsp; ASSESSMENT\_QUESTIONS ||--o{ ASSESSMENT\_OPTIONS : has

&nbsp; ASSESSMENTS ||--o{ ASSESSMENT\_ATTEMPTS : has

&nbsp; USERS ||--o{ ASSESSMENT\_ATTEMPTS : does

&nbsp; ASSESSMENT\_ATTEMPTS ||--o{ ASSESSMENT\_ANSWERS : has



&nbsp; USERS ||--o{ ACTIVITY\_EVENTS : logs



&nbsp; LIBRARY\_CATEGORIES ||--o{ LIBRARY\_RESOURCES : has

&nbsp; RESOURCE\_TYPES ||--o{ LIBRARY\_RESOURCES : has



&nbsp; ANNOUNCEMENTS ||--o{ ANNOUNCEMENT\_TARGETS : targets



&nbsp; SCORECARD\_TEMPLATES ||--o{ SCORECARD\_FIELDS : has

&nbsp; SCORECARD\_TEMPLATES ||--o{ SCORECARD\_ENTRIES : has

&nbsp; SCORECARD\_ENTRIES ||--o{ SCORECARD\_VALUES : has

