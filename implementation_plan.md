# Fan Content Hub Platform Extension - Implementation Plan

## Goal Description
Enhance the existing template into a Fan Content Hub Platform where admins can manage videos, images, events, and tags, while public users can browse and filter content. The core features involve migrating media uploads to use Google Drive dynamically via queue jobs, adding PostgreSQL GIN indexes for full-text search, and strictly adhering to the existing repository, service, and API resource architecture using only `id_hash` for public endpoints.

## Proposed Changes

---

### Database Layer (Migrations)
#### [NEW] database/migrations/xxxx_xx_xx_xxxxxx_create_events_table.php
- **Schema**: [id](file:///c:/laragon/www/fan-content-hub-platform/resources/js/components/select-single/Index.vue#144-149) (PK), `id_hash` (uuid), `name`, `slug`, [date](file:///c:/laragon/www/fan-content-hub-platform/app/Http/Controllers/ApiWeb/MasterUserController.php#197-254) (date), `location`, `description` (text), timestamps.

#### [NEW] database/migrations/xxxx_xx_xx_xxxxxx_create_tags_table.php
- **Schema**: [id](file:///c:/laragon/www/fan-content-hub-platform/resources/js/components/select-single/Index.vue#144-149) (PK), `id_hash` (uuid), `name`, `slug`, timestamps.

#### [NEW] database/migrations/xxxx_xx_xx_xxxxxx_create_contents_table.php
- **Schema**: [id](file:///c:/laragon/www/fan-content-hub-platform/resources/js/components/select-single/Index.vue#144-149) (PK), `id_hash` (uuid), `title`, `type` (enum: image, video), `event_id` (FK), `google_drive_file_id` (nullable string), `google_drive_url` (nullable string), `metadata` (JSONB), `status` (enum: pending, active, inactive), timestamps.
- **Indexes**: `event_id`.

#### [NEW] database/migrations/xxxx_xx_xx_xxxxxx_create_content_tag_table.php
- **Schema**: `content_id` (FK), `tag_id` (FK), composite primary key.

#### [NEW] database/migrations/xxxx_xx_xx_xxxxxx_add_fulltext_search_indexes.php
- Add PostgreSQL `tsvector` generated columns and GIN indexes to `contents` (`title`) and `events` (`name`, `description`).

---

### Google Drive & Queue Architecture
#### [NEW] app/Services/GoogleDriveService.php
- Methods: `createFolderIfNotExists(string $year, string $idol, string $event)`, `uploadFile($fileStream, $folderId, $metadata)`, `setPublicPermission($fileId)`, `getPublicUrl($fileId)`.
- Use the Google API Client (`google/apiclient`) or configured Flysystem Google Drive adapter.

#### [NEW] app/Jobs/UploadImageToDriveJob.php
- Implement retry logic and logging via the `ShouldQueue` interface.
- Executes: Create folder structure, upload file, set permissions to public, retrieve Drive URL/ID, and update the `<Content>` record to active.

---

### Repository & Service Layer
#### [NEW] app/Interfaces/EventInterface.php & app/Repositories/EventRepository.php
- Standard data access logic using the `getAll`, `findByIdHash` base methods seen in [MasterUserController](file:///c:/laragon/www/fan-content-hub-platform/app/Http/Controllers/ApiWeb/MasterUserController.php#14-303) pattern.

#### [NEW] app/Interfaces/ContentInterface.php & app/Repositories/ContentRepository.php
- Add specific PostgreSQL full-text search scope for content filtering using `WHERE to_tsvector(...) @@ to_tsquery(...)`. Filtering by year, idol, and category will run here.

#### [NEW] app/Services/ContentService.php
- Processes the upload request.
- Saves initial record with `status: pending`.
- Dispatches the `UploadImageToDriveJob`.

#### [NEW] app/Interfaces/TagInterface.php & app/Repositories/TagRepository.php
- Standard CRUD logic.

---

### Controllers & API Resources
#### [NEW] app/Http/Controllers/ApiWeb/EventController.php
- Admin CMS endpoints for managing events. Follows [MasterUserController](file:///c:/laragon/www/fan-content-hub-platform/app/Http/Controllers/ApiWeb/MasterUserController.php#14-303) injection and validation patterns.
- Returns `EventResource`.

#### [NEW] app/Http/Controllers/ApiWeb/TagController.php
- Admin CMS endpoints for managing tags.

#### [NEW] app/Http/Controllers/ApiWeb/ContentController.php
- Admin CMS endpoint for content management and initial upload processing.
- Offloads upload to `ContentService`.

#### [NEW] app/Http/Controllers/ApiPublic/FanContentController.php
- Public browse endpoint (no auth required).
- Search, Filter (Year, Idol, Category), Pagination.
- Returns `ContentResource` collections.

#### [NEW] app/Http/Resources/EventResource.php
- Maps [id](file:///c:/laragon/www/fan-content-hub-platform/resources/js/components/select-single/Index.vue#144-149) to `id_hash`, formats dates, serializes relations.

#### [NEW] app/Http/Resources/ContentResource.php
- Maps [id](file:///c:/laragon/www/fan-content-hub-platform/resources/js/components/select-single/Index.vue#144-149) to `id_hash`, exposes `google_drive_url`.

---

### Frontend
#### [NEW] resources/js/views/admin/events/* (and contents/tags)
- Replicate existing standard index, form, and table patterns.
#### [NEW] resources/js/views/public/*
- Implement gallery interfaces, timelines, search components connected to `/api-public/` endpoints.

---

## Verification Plan

### Automated Tests
- Run `php artisan test` covering the new Repositories, ensuring `findByIdHash` uniquely resolves the right models.
- Build feature tests for CMS API endpoints covering all CRUD actions and ensuring `id_hash` is correctly asserted in API JSON response.
- Build feature test for `FanContentController` ensuring public data retrieval and accurate full text search filtering limits.

### Manual Verification
1. Open the local Vue application locally via `npm run dev`. Navigate to the Admin route.
2. Form testing: Ensure an image upload form correctly hits the Content endpoint.
3. Queue testing: Run `php artisan queue:work`. Check logs to verify `UploadImageToDriveJob` dispatches, queries Google Drive successfully, and modifies the Content record's Google Drive fields correctly.
4. Database Verification: Inspect Postgres to verify full_text search vectors were successfully created and updated.
5. Browse the Public UI to confirm images load from Google Drive URLs correctly.
