Ext.onReady(function() {
    MODx.load({ xtype: 'campaigner-page-home'});
});

Campaigner.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'campaigner-panel-main'
            ,renderTo: 'campaigner-panel-home-div'
        }]
    }); 
    Campaigner.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.page.Home,MODx.Component);
Ext.reg('campaigner-page-home',Campaigner.page.Home);