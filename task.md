# Fan Content Hub Platform Extension Task List

## Planning
- [ ] Analyze existing architectural patterns (`MasterUserController`, `ExampleFileUploadController`).
- [ ] Draft database schema (migrations for events, contents, tags, content_tag).
- [ ] Draft `implementation_plan.md` to present to the user.

## Execution
- [ ] **Database & Models**
    - [ ] Create Migrations & Models for `Event`, `Content`, `Tag`, `ContentTag`.
    - [ ] Setup `id_hash` logic.
    - [ ] Setup PostgreSQL GIN indexes for full-text search.
- [ ] **Google Drive Integration**
    - [ ] Setup Google Drive Storage Disk.
    - [ ] Create `GoogleDriveService`.
    - [ ] Create `UploadImageToDriveJob` queue job.
- [ ] **Repositories & Services**
    - [ ] Implement `EventRepository` and `EventService`.
    - [ ] Implement `ContentRepository` and `ContentService` (handling search & filtering).
    - [ ] Implement `TagRepository` and `TagService`.
- [ ] **Controllers & API Resources**
    - [ ] Implement `EventResource`, `ContentResource`, `TagResource`.
    - [ ] Implement CMS Admin Controllers (CRUD for events, contents, tags).
    - [ ] Implement Public Controllers (Browsing, filtering).
- [ ] **Frontend (Vue 3)**
    - [ ] CMS Views (Events, Contents, Tags management).
    - [ ] Public Views (Gallery, Event timelines).

## Verification
- [ ] Test Upload Queue Jobs.
- [ ] Test Search & Filter logic.
- [ ] Verify `id_hash` usage everywhere.
