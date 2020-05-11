(function (global, eZ) {
    eZ = eZ || {};
    eZ.adminUiConfig = eZ.adminUiConfig || {};
    eZ.adminUiConfig.universalDiscoveryWidget = eZ.adminUiConfig.universalDiscoveryWidget || {};
    eZ.adminUiConfig.universalDiscoveryWidget.extraTabs = eZ.adminUiConfig.universalDiscoveryWidget.extraTabs || [];

    eZ.adminUiConfig.universalDiscoveryWidget.extraTabs.push({
        id: 'images',
        title: 'Images',
        iconIdentifier: 'image',
        panel: eZ.modules.ImagesPanel, // React component that represents content of a tab
        attrs: {}
    });

})(window, window.eZ);