import { IDataTable } from "./global";
import { IEvent } from "./fch-event";
import { ITag } from "./fch-tag";

export interface IContent {
    id: string; // id_hash
    title: string;
    type: 'image' | 'video';
    status: 'pending' | 'active' | 'inactive';
    google_drive_url: string | null;
    event?: IEvent;
    tags?: ITag[];
}

export interface IDataTableContent extends IDataTable {
    data: IContent[];
}

export interface IContentDetail extends IContent {
    createdAt: string;
}

export interface ICreateContentPayload {
    title: string;
    type: string;
    event_id: string; // event id (normal) or we handle conversion on backend. Backend takes `exists:events,id` but we usually send the real ID from the select dropdown. Let's type it as string.
    files: File[];
    tags?: string[]; // array of tag IDs
}

export interface IEditContentPayload {
    title: string;
    status: string;
    tags?: string[];
}