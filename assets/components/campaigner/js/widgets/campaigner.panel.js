Campaigner.panel.Main = function(config) {
    config = config || {};

    var tabs = [];
    if (MODx.perm.tab_newsletter) {
       tabs.push({
            layout: 'card'
            ,title: _('campaigner.newsletter')
            ,defaults: { autoHeight: true }
            ,id: 'card-container'
            ,itemId: 'campaigner-tab-newsletter'
            ,activeItem: 0
            ,items: [{
                xtype: 'container'
                ,id: 'campaigner-panel-newsletter-base'
                
                ,items: [{
                    html: '<p>'+_('campaigner.newsletter.info')+'</p>'
                    ,border: false
                    ,bodyStyle: 'padding: 10px'    
                },{
                    xtype: 'campaigner-grid-newsletter'
                    ,id: 'campaigner-grid-newsletter'
                    ,preventRender: true
                }]
            },{
                xtype: 'campaigner-panel-newsletter-editarticles',
                id: 'campaigner-panel-newsletter-editarticles',
                preventRender: true
            }]
        });
    }

    if (MODx.perm.tab_autonewsletter) {
       tabs.push({
            title: _('campaigner.autonewsletter'),
            defaults: { autoHeight: true },
            id: 'campaigner-tab-autonewsletter',
            itemId: 'campaigner-tab-autonewsletter',
            items: [{
                html: '<p>'+_('campaigner.autonewsletter.info')+'</p>',
                border: false,
                bodyStyle: 'padding: 10px'
            },{
                xtype: 'campaigner-grid-autonewsletter',
                id: 'campaigner-grid-autonewsletter',
                preventRender: true
            }]
        });
    }

    if (MODx.perm.tab_groups) {
       tabs.push({
            title: _('campaigner.groups'),
            defaults: { autoHeight: true },
            id: 'campaigner-tab-groups',
            itemId: 'campaigner-tab-groups',
            items: [{
                html: '<p>'+_('campaigner.groups.info')+'</p>',
                border: false,
                bodyStyle: 'padding: 10px'
            },{
                xtype: 'campaigner-grid-group',
                id: 'campaigner-grid-group',
                preventRender: true
            }]
        });
    }

    if (MODx.perm.tab_subscriber) {
       tabs.push({
            title: _('campaigner.subscribers'),
            defaults: { autoHeight: true },
            id: 'campaigner-tab-subscriber',
            itemId: 'campaigner-tab-subscriber',
            items: [{
                html: '<p>'+_('campaigner.subscribers.info')+'</p>',
                border: false,
                bodyStyle: 'padding: 10px'
            },{
                xtype: 'campaigner-grid-subscriber',
                id: 'campaigner-grid-subscriber',
                preventRender: true
            }]
        });
    }

    // if (MODx.perm.tab_bounces) {
       tabs.push({
            title: _('campaigner.bounce'),
            defaults: { autoHeight: true },
            id: 'campaigner-tab-bounce',
            itemId: 'campaigner-tab-bounce',
            items: [{
                xtype: 'button'
                ,style: 'margin: 10px'
                ,text: _('campaigner.bounce.fetch')
                ,handler: this.fetchBounces
            },{
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
        });
    // }

    if (MODx.perm.tab_queue) {
       tabs.push({
            title: _('campaigner.queue'),
            defaults: { autoHeight: true },
            id: 'campaigner-tab-queue',
            itemId: 'campaigner-tab-queue',
            items: [{
                html: '<p>'+_('campaigner.queue.info')+'</p>',
                border: false,
                bodyStyle: 'padding: 10px'
            },{
                xtype: 'campaigner-grid-queue',
                id: 'campaigner-grid-queue',
                preventRender: true
            }]
        });
    }

    if (MODx.perm.tab_statistics) {
       tabs.push({
            title: _('campaigner.statistics'),
            defaults: { autoHeight: true },
            id: 'campaigner-tab-statistics',
            itemId: 'campaigner-tab-statistics',
            items: [{
                html: '<p>'+_('campaigner.statistics.info')+'</p>',
                border: false,
                bodyStyle: 'padding: 10px'
            },{
                xtype: 'campaigner-grid-statistics',
                id: 'campaigner-grid-statistics',
                preventRender: true
            }]
        });
    }

    if (MODx.perm.tab_sharing) {
       tabs.push({
            title: _('campaigner.sharing'),
            defaults: { autoHeight: true },
            id: 'campaigner-tab-sharing',
            itemId: 'campaigner-tab-sharing',
            // disabled: true,
            items: [{
                html: '<p>'+_('campaigner.sharing.info')+'</p>',
                border: false,
                bodyStyle: 'padding: 10px'
            },{
                xtype: 'campaigner-grid-socialsharing',
                id: 'campaigner-grid-socialsharing',
                preventRender: true
            }]
        });
    }

    if (MODx.perm.tab_fields) {
       tabs.push({
            title: _('campaigner.fields'),
            defaults: { autoHeight: true },
            id: 'campaigner-tab-fields',
            itemId: 'campaigner-tab-fields',
            // disabled: true,
            items: [{
                html: '<p>'+_('campaigner.fields.info')+'</p>',
                border: false,
                bodyStyle: 'padding: 10px'
            },{
                xtype: 'campaigner-grid-fields',
                id: 'campaigner-grid-fields',
                preventRender: true
            }]
        });
    }

    if (MODx.perm.tab_autoresponders) {
       tabs.push({
            title: _('campaigner.autoresponders'),
            defaults: { autoHeight: true },
            id: 'campaigner-tab-autoresponders',
            itemId: 'campaigner-tab-autoresponders',
            // disabled: true,
            items: [{
                html: '<p>'+_('campaigner.autoresponders.info')+'</p>',
                border: false,
                bodyStyle: 'padding: 10px'
            },{
                xtype: 'campaigner-grid-autoresponders',
                id: 'campaigner-grid-autoresponders',
                preventRender: true
            }]
        });
    }

    Ext.apply(config,
    {
        border: false,
        baseCls: 'modx-formpanel',
        items: [{
            html: '<h2>'+_('campaigner.fulltitle')+'</h2>',
            border: false,
            cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,bodyStyle: 'padding: 10px'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,items: tabs
            ,listeners: {
                'render': {fn: function(tabpanel) {
                    var token = window.location.hash.substr(1);
                    if(token) {
                        var tab = tabpanel.get(token);
                        tabpanel.setActiveTab(tab);
                    }
                }, scope: this }
            }
        }]
        
    });
    Campaigner.panel.Main.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.panel.Main,MODx.Panel, {
    fetchBounces: function() {
        if (this.console === null || this.console === undefined) {
            this.console = MODx.load({
               xtype: 'modx-console'
               ,register: register
               ,topic: topic
               ,show_filename: 0
               ,listeners: {
                 'shutdown': {fn:function() {
                     // Ext.getCmp('modx-layout').refreshTrees();
                 },scope:this}
               }
            });
        } else {
            this.console.setRegister(register, topic);
        }
        this.console.show(Ext.getBody());

        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/bounce/fetch',
                register: register,
                topic: topic
            }
            ,listeners: {
                'success': {fn:function() {
                    this.console.fireEvent('complete');
                }, scope:this}
            }
        });
    }
});
Ext.reg('campaigner-panel-main',Campaigner.panel.Main);