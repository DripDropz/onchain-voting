import Pagination from "./pagination";

export default interface PaginatedResponse<T> {
    data: T[];
    meta: Pagination;
}
