import UserData = App.DataTransferObjects.UserData;

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: UserData;
    };
};
