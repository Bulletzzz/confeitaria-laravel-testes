import { defineConfig } from 'cypress';
import { execFileSync } from 'node:child_process';

export default defineConfig({
    viewportWidth: 1280,
    viewportHeight: 900,
    video: false,
    taskTimeout: 120000,
    e2e: {
        baseUrl: 'http://localhost:8000',
        setupNodeEvents(on, config) {
            on('task', {
                'db:seed'() {
                    if (config.baseUrl !== 'http://localhost:8001') {
                        throw new Error('Use npm run cy:run ou npm run cy:open: os testes usam a porta 8001 e o banco confeitaria_e2e.');
                    }
                    const compose = ['compose', '--profile', 'testing', 'exec', '-T', 'app-e2e'];
                    const options = { cwd: config.projectRoot, encoding: 'utf8', timeout: 110000 };
                    const database = execFileSync('docker', [...compose, 'printenv', 'DB_DATABASE'], options).trim();
                    if (database !== 'confeitaria_e2e') {
                        throw new Error('Reset recusado: o container precisa usar o banco confeitaria_e2e.');
                    }
                    return execFileSync('docker', [...compose, 'php', 'artisan', 'migrate:fresh', '--seed', '--force', '--no-interaction'], options);
                },
            });
            return config;
        },
    },
});
