const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('api', {
    getAppMode: () => ipcRenderer.invoke('get-app-mode'),
    
    previewSchemaFile: (schemaJson) => ipcRenderer.invoke('preview-schema-file', schemaJson),

    
    saveArtiframeFile: (globalDbData) => ipcRenderer.invoke('save-artiframe-file', globalDbData),
    loadArtiframeFile: () => ipcRenderer.invoke('load-artiframe-file'),

    getWorkspace: () => ipcRenderer.invoke('get-workspace'),
    setWorkspace: (dirPath) => ipcRenderer.invoke('set-workspace', dirPath),
    selectDirectory: () => ipcRenderer.invoke('select-directory'),
    getProjects: (workspacePath) => ipcRenderer.invoke('get-projects', workspacePath),
    openProject: (pPath) => ipcRenderer.invoke('open-project', pPath),

    getSchema: () => ipcRenderer.invoke('get-schema'),
    onAppMode: (callback) => ipcRenderer.on('set-app-mode', (event, data) => callback(data)),
    restartOffline: () => ipcRenderer.invoke('restart-offline'),
    scaffoldProject: (dirName, schemaJson, customWorkspace) => ipcRenderer.invoke('scaffold-project', dirName, schemaJson, customWorkspace),

    executeSql: (sql) => ipcRenderer.invoke('execute-sql', sql),
    saveLayout: (layoutData) => ipcRenderer.invoke('save-layout', layoutData),
    getTableData: (tableName, limit, offset) => ipcRenderer.invoke('get-table-data', tableName, limit, offset),
    updateRow: (tableName, pkCol, pkVal, data) => ipcRenderer.invoke('update-row', tableName, pkCol, pkVal, data),
    deleteRow: (tableName, pkCol, pkVal) => ipcRenderer.invoke('delete-row', tableName, pkCol, pkVal),
    seedTable: (tableName, columns, count) => ipcRenderer.invoke('seed-table', tableName, columns, count)
});
