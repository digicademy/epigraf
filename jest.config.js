module.exports = {
    transform: {
        '^.+\\.js$': 'babel-jest',
    },
    testMatch: [
        '**/tests/Jest/**/*.test.js', // 👈 tells Jest to look in /tests/Jests
    ],
    moduleFileExtensions: ['js'],
    transformIgnorePatterns: ['/node_modules/'],
};
