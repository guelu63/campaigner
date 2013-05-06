Campaigner.panel.Main = function(config) {
    config = config || {};
    Ext.apply(config,
    {
        border: false,
        baseCls: 'modx-formpanel',
        items: [{
            html: '<h2>'+_('campaigner.fulltitle')+'</h2>',
            border: false,
            cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs',
            bodyStyle: 'padding: 10px',
            defaults: { border: false ,autoHeight: true },
            border: true,
            items: [{
                title: _('campaigner.newsletter'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-newsletter',
                items: [{
                    html: '<p>'+_('campaigner.newsletter.info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                },{
                    xtype: 'campaigner-grid-newsletter',
                    id: 'campaigner-grid-newsletter',
                    preventRender: true
                }]
            }, {
                title: _('campaigner.autonewsletter'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-autonewsletter',
                items: [{
                    html: '<p>'+_('campaigner.autonewsletter.info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                },{
                    xtype: 'campaigner-grid-autonewsletter',
                    id: 'campaigner-grid-autonewsletter',
                    preventRender: true
                }]
            }, {
                title: _('campaigner.groups'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-groups',
                items: [{
                    html: '<p>'+_('campaigner.groups.info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                },{
                    xtype: 'campaigner-grid-group',
                    id: 'campaigner-grid-group',
                    preventRender: true
                }]
            }, {
                title: _('campaigner.subscribers'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-subscriber',
                items: [{
                    html: '<p>'+_('campaigner.subscribers.info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                },{
                    xtype: 'campaigner-grid-subscriber',
                    id: 'campaigner-grid-subscriber',
                    preventRender: true
                }]
            },  {
                title: _('campaigner.bounce'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-bounce',
                items: [{
                    xtype: 'modx-tabs',
                    bodyStyle: 'padding: 10px',
                    defaults: { border: false ,autoHeight: true },
                    border: true,
                    items: [{
                        title: _('campaigner.bounce.hard'),
                        defaults: { autoHeight: true },
                        id: 'campaigner-tab-bounce-hard',
                        items: [{
                            html: '<p>'+_('campaigner.bounce.hard.info')+'</p>',
                            border: false,
                            bodyStyle: 'padding: 10px'
                            },{
                            xtype: 'campaigner-grid-bounce-hard',
                            id: 'campaigner-grid-bounce-hard',
                            preventRender: true
                        }]
                    },{
                    title: _('campaigner.bounce.soft'),
                    defaults: { autoHeight: true },
                    id: 'campaigner-tab-bounce-soft',
                    items: [{
                        html: '<p>'+_('campaigner.bounce.soft.info')+'</p>',
                        border: false,
                        bodyStyle: 'padding: 10px'
                        },{
                        xtype: 'campaigner-grid-bounce-soft',
                        id: 'campaigner-grid-bounce-soft',
                        preventRender: true
                    }]
                    },{
                    title: _('campaigner.bounce.resend'),
                    defaults: { autoHeight: true },
                    id: 'campaigner-tab-bounce-resend',
                    items: [{
                        html: '<p>'+_('campaigner.bounce.resend.info')+'</p>',
                        border: false,
                        bodyStyle: 'padding: 10px'
                        },{
                        xtype: 'campaigner-grid-bounce-resend',
                        id: 'campaigner-grid-bounce-resend',
                        preventRender: true
                    }]
                    }]
                    }]
            },{
                title: _('campaigner.queue'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-queue',
                items: [{
                    html: '<p>'+_('campaigner.queue.info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                },{
                    xtype: 'campaigner-grid-queue',
                    id: 'campaigner-grid-queue',
                    preventRender: true
                }]
            },{
                title: _('campaigner.statistics'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-statistics',
                items: [{
                    html: '<p>'+_('campaigner.statistics.info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                },{
                    xtype: 'campaigner-grid-statistics',
                    id: 'campaigner-grid-statistics',
                    preventRender: true
                }]
            }]
        }]
    });
    Campaigner.panel.Main.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.panel.Main,MODx.Panel);
Ext.reg('campaigner-panel-main',Campaigner.panel.Main);