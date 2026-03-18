import { IDataTable } from "./global";

export interface ITag {
    id: string; // id_hash
    name: string;
    slug: string;
}

export interface IDataTableTag extends IDataTable {
    data: ITag[];
}

export interface ITagDetail extends ITag {
    createdAt: string;
}

export interface ICreateTagPayload {
    name: string;
}

export interface IEditTagPayload {
    name: string;
}