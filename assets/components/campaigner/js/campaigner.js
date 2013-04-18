var Campaigner = function(config) {
    config = config || {};
    Campaigner.superclass.constructor.call(this,config);
};

Ext.extend(Campaigner, Ext.Component, {
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});

Ext.reg('campaigner', Campaigner);

var Campaigner = new Campaigner();