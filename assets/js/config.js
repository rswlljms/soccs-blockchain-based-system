let AppConfig = {
    blockchainUrl: 'http://localhost:3001',
    initialized: false
};

async function loadConfig() {
    try {
        const response = await fetch('../api/get_config.php');
        const config = await response.json();
        AppConfig.blockchainUrl = config.blockchainUrl || 'http://localhost:3001';
        AppConfig.initialized = true;
    } catch (error) {
        console.warn('Failed to load config, using default:', error);
        AppConfig.initialized = true;
    }
}

loadConfig();

