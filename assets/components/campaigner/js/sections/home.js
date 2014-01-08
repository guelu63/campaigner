Ext.onReady(function() {

    // var token = window.location.hash.substr(1);

    // if ( token ) {
    //     var tab = tabPanel.get(token);

    //     if ( ! tab ) {
    //         // Create tab or error as necessary.
    //         tab = new Ext.Panel({
    //             itemId: token,
    //             title: 'Tab: '+ token
    //         });

    //         tabPanel.add(tab);
    //     }

    //     tabPanel.setActiveTab(tab);
    // }
    MODx.load({ xtype: 'campaigner-page-home'});
});

Campaigner.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'campaigner-panel-main',
            renderTo: 'campaigner-panel-home-div'
        }]
    });
    Campaigner.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.page.Home,MODx.Component);
Ext.reg('campaigner-page-home',Campaigner.page.Home);