export const metadata = {
    lib: {
        user: {
            firstname: 'Jane',
            lastname: 'Doe',
            institution: 'Test University',
        },
        licenses: [
            {value: 'cc-by', text: 'CC BY'},
            {value: 'cc-by-sa', text: 'CC BY-SA'},
        ],
        placeholder: 'Select a license',
    },
    container: {
        update: jest.fn(),
        revert: jest.fn(),
    },
    jsonStringify: jest.fn(),
    jsonToState: jest.fn(),
    state: {},
    loadState: jest.fn(),
};
