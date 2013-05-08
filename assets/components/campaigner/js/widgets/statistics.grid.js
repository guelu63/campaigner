Campaigner.grid.Statistics = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url,
        baseParams: { action: 'mgr/statistics/getList', showProcessed: 0 },
        fields: ['id', 'pagetitle', 'subscriber', 'newsletter', 'state', 'date', 'hits', 'sent', 'sent_date', 'bounced', 'total', 'priority', 'opened', 'perc_open'],
        paging: true,
        autosave: false,
        remoteSort: true,
        primaryKey: 'id',
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoScroll: true,
            emptyText: _('campaigner.grid.no_data')
        },
        sm: this.sm,
        columns: [{
            header: _('campaigner.newsletter.id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 5
            // ,renderer: this._renderSubscriber
        },{
            header: _('campaigner.newsletter')
            ,dataIndex: 'pagetitle'
            ,sortable: true
            ,width: 25
            ,renderer: this._renderNewsletter
        },{
            header: _('campaigner.newsletter.sent_date')
            ,dataIndex: 'sent_date'
            ,sortable: true
            ,width: 15
            // ,renderer: this._renderSubscriber
        },{
            header: _('campaigner.newsletter.total')
            ,dataIndex: 'total'
            ,sortable: true
            ,width: 8
            ,renderer: this._renderSubscriber
        },{
            header: _('campaigner.statistics.opened')
            ,dataIndex: 'perc_open'
            ,sortable: true
            ,width: 8
            ,renderer: this._renderOpened
        },{
            header: _('campaigner.statistics.hits')
            ,dataIndex: 'hits'
            ,sortable: true
            ,width: 8
            ,renderer: this._renderHits
        },{
            header: _('campaigner.statistics.bounced')
            ,dataIndex: 'state'
            ,sortable: true
            ,width: 5
            ,renderer: this._renderState
        },{
            header: _('campaigner.statistics.sent')
            ,dataIndex: 'sent'
            ,sortable: true
            ,width: 10
        }]
        // /* Top toolbar */
        // tbar : [
        //     {
        //         xtype: 'textfield'
        //         ,name: 'search'
        //         ,id: 'campaigner-filter-search'
        //         ,emptyText: _('search')+'...'
        //         ,listeners: {
        //         'change': {fn: this.filterSearch, scope: this}
        //         ,'render': {fn: function(cmp) {
        //             new Ext.KeyMap(cmp.getEl(), {
        //                 key: Ext.EventObject.ENTER
        //                 ,fn: function() {
        //                     this.fireEvent('change',this.getValue());
        //                     this.blur();
        //                     return true;
        //                 },scope: cmp
        //             });
        //         },scope:this}
        //     }
        // }]
    });
    Campaigner.grid.Statistics.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.grid.Statistics, MODx.grid.Grid);
Ext.reg('campaigner-grid-statistics', Campaigner.grid.Statistics);

Ext.extend(Campaigner.grid.Statistics,MODx.grid.Grid,{
    _renderOpened: function(value, p, rec) {
        return rec.data.perc_open + '%';
    }
    ,_renderHits: function(value, p, rec) {
        return rec.data.hits + ' (' + rec.data.subscriber + ')';
    }
    ,getMenu: function() {
        var m = [];
        if (this.getSelectionModel().getCount() == 1) {
            var rs = this.getSelectionModel().getSelections();

            m.push({
                text: _('campaigner.statistics.show_details')
                ,handler: this.showDetails
            });
            // m.push('-');
            // if(this.menu.record.active != 1) {
            //   m.push({
            //       text: _('campaigner.subscriber.activate')
            //       ,handler: this.activateSubscriber
            //   });
            // } else {
            //   m.push({
            //       text: _('campaigner.subscriber.deactivate')
            //       ,handler: this.deactivateSubscriber
            //   });
            // }
            // m.push('-');
            // m.push({
            //     text: _('campaigner.subscriber.remove')
            //     ,handler: this.removeSubscriber
            // });
        }
        if (m.length > 0) {
            this.addContextMenuItem(m);
        }
    }

    ,showDetails: function(btn,e) {
        if (!this.updateStatisticsWindow) {
            this.updateStatisticsWindow = MODx.load({
                xtype: 'campaigner-window-statistics-details'
                ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        }

        this.updateStatisticsWindow.setValues(this.menu.record);
        this.updateStatisticsWindow.show(e.target);

        var grid_open = Ext.getCmp('campaigner-grid-statistics-details-open');
        grid_open.store.load({params:{statistics_id: this.menu.record.id || 0}});

        var grid_hits = Ext.getCmp('campaigner-grid-statistics-details-hits');
        grid_hits.store.load({params:{statistics_id: this.menu.record.id || 0}});

        var grid_bounces = Ext.getCmp('campaigner-grid-statistics-details-bounces');
        grid_bounces.store.load({params:{statistics_id: this.menu.record.id || 0}});
        // var grid_open = Ext.getCmp('campaigner-grid-statistics-details-open');
        // grid_open.store.load({params:{statistics_id: this.menu.record.id || 0}});
    }
});

Campaigner.window.Statistics = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('campaigner.statistics_details') + ' - ' + config.record.pagetitle
        ,width: 850
        ,height: 500
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/statistics/details'
        }
        ,items: [{
            xtype: 'modx-tabs',
            bodyStyle: 'padding: 10px',
            defaults: { autoHeight: true },
            border: true,
            style: 'padding:10px',
            fields: [{
                xtype: 'hidden'
                ,name: 'id'
            }],
            items: [
            {
                title: _('campaigner.statistics.open'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-statistics-details-open',
                items: [{
                    html: '<p>'+_('campaigner.statistics.open_info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                },{
                    xtype: 'campaigner-grid-statistics-details-open'
                    // ,fieldLabel: _('campaigner.statistics_details')
                    ,id: 'campaigner-grid-statistics-details-open'
                    ,scope: this
                    ,preventRender: true
                }]
            },{
                title: _('campaigner.statistics.hits'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-statistics-details-hits',
                items: [{
                    html: '<p>'+_('campaigner.statistics.hits_info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                },{
                    xtype: 'campaigner-grid-statistics-details-hits'
                    // ,fieldLabel: _('campaigner.statistics_details')
                    ,id: 'campaigner-grid-statistics-details-hits'
                    ,scope: this
                    ,preventRender: true
                }]
            },{
                title: _('campaigner.statistics.bounces'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-statistics-details-bounces',
                items: [{
                    html: '<p>'+_('campaigner.statistics.bounces_info')+'<br/>UNDER CONSTRUCTION</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                },{
                    xtype: 'campaigner-grid-statistics-details-bounces'
                    // ,fieldLabel: _('campaigner.statistics_details')
                    ,id: 'campaigner-grid-statistics-details-bounces'
                    ,scope: this
                    ,preventRender: true
                }]
            },{
                title: _('campaigner.statistics.unsubcribers'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-statistics-details-unsubscribers',
                items: [{
                    html: '<p>'+_('campaigner.statistics.unsubcribers_info')+'<br/>UNDER CONSTRUCTION</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                }]
            }]
        }]
        ,buttons: [{
            text: _('close')
            ,scope: this
            ,handler: function() { this.hide(); }
        }]
        // ,fields: [{
        //     xtype: 'campaigner-grid-statistics-details'
        //     ,fieldLabel: _('campaigner.statistics_details')
        // }]
    });
    // this.ident = config.ident || 'campaigner-'+Ext.id();
    Campaigner.window.Statistics.superclass.constructor.call(this,config);
};


Ext.extend(Campaigner.window.Statistics,MODx.Window);
Ext.reg('campaigner-window-statistics-details',Campaigner.window.Statistics);

Campaigner.grid.StatisticsDetailsOpen = function(config) {
    config = config || {};
    console.log(config);
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        id: 'campaigner-grid-statistics-details-open'
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/statistics/details',
            open: 1
        }
        ,viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoScroll: true,
            emptyText: _('campaigner.grid.no_data')
        }
        ,primaryKey: 'id'
        ,sm: this.sm
        ,fields: ['id','link','email', 'hit_date', 'view_total']
        ,paging: true
        ,remoteSort: true
        ,columns: [{
            header: _('campaigner.statistics.link')
            ,dataIndex: 'link'
            ,sortable: true
            ,width: 25
        },{
            header: _('campaigner.subscriber')
            ,dataIndex: 'email'
            ,sortable: true
            ,width: 25
        },{
            header: _('campaigner.statistics.hit_date')
            ,dataIndex: 'hit_date'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.statistics.open')
            ,dataIndex: 'view_total'
            ,sortable: true
            ,width: 10
        }],
        tbar : [{
            xtype: 'textfield'
            ,name: 'search-details'
            ,id: 'campaigner-filter-search'
            ,emptyText: _('search')+'...'
            ,listeners: {
                'change': {fn: this.filterDetails, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;}
                        ,scope: cmp
                    });
                },scope:this}
            }
        }]
    });
    Campaigner.grid.StatisticsDetailsOpen.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.grid.StatisticsDetailsOpen,MODx.grid.Grid, {
    filterDetails: function(tf,newValue,oldValue) {
        var nv = newValue;
        this.getStore().baseParams.search = nv;
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
});

Ext.reg('campaigner-grid-statistics-details-open',Campaigner.grid.StatisticsDetailsOpen);

Campaigner.grid.StatisticsDetailsHits = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        id: 'campaigner-grid-statistics-details-hits'
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/statistics/details'
        }
        ,viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoScroll: true,
            emptyText: _('campaigner.grid.no_data')
        }
        ,sm: this.sm
        ,fields: ['id','link','email', 'hit_date', 'view_total']
        ,paging: true
        ,remoteSort: true
        ,columns: [{
            header: _('campaigner.statistics.link')
            ,dataIndex: 'link'
            ,sortable: true
            ,width: 50
        },{
            header: _('campaigner.subscriber')
            ,dataIndex: 'email'
            ,sortable: true
            ,width: 40
        },{
            header: _('campaigner.statistics.hit_date')
            ,dataIndex: 'hit_date'
            ,sortable: true
            ,width: 30
        },{
            header: _('campaigner.statistics.hits')
            ,dataIndex: 'view_total'
            ,sortable: true
            ,width: 10
        }],
        tbar : [{
            xtype: 'textfield'
            ,name: 'search-details'
            ,id: 'campaigner-filter-search'
            ,emptyText: _('search')+'...'
            ,listeners: {
                'change': {fn: this.filterDetails, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;}
                        ,scope: cmp
                    });
                },scope:this}
            }
        }]
    });
    Campaigner.grid.StatisticsDetailsHits.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.grid.StatisticsDetailsHits,MODx.grid.Grid, {
    filterDetails: function(tf,newValue,oldValue) {
        var nv = newValue;
        this.getStore().baseParams.search = nv;
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
});

Ext.reg('campaigner-grid-statistics-details-hits',Campaigner.grid.StatisticsDetailsHits);


Campaigner.grid.StatisticsDetailsBounces = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        id: 'campaigner-grid-statistics-details-bounces'
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/statistics/bounce'
        }
        ,viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoScroll: true,
            emptyText: _('campaigner.grid.no_data')
        }
        ,sm: this.sm
        ,fields: ['id', 'subscriber', 'name', 'email', 'type', 'count', 'last', 'active', 'reason', { name : 'recieved', type: 'date', dateFormat: 'timestamp'}, 'code']
        ,paging: true
        ,remoteSort: true
        ,columns: [this.sm,{
            header: _('campaigner.bounce.name')
            ,dataIndex: 'name'
            //,width: 10
        },{
            header: _('campaigner.subscriber.email')
            ,dataIndex: 'email'
            //,width: 10
        },{
            header: _('campaigner.subscriber.active')
            ,dataIndex: 'active'
            //,width: 10
            ,renderer: this._renderActive
        },{
            header: _('campaigner.bounce.type')
            ,dataIndex: 'type'
            //,width: 10
        },{
            header: _('campaigner.bounce.code')
            ,dataIndex: 'code'
            //,width: 10
        },{
            header: _('campaigner.bounce.reason')
            ,dataIndex: 'reason'
            ,sortable: true
            //,width: 10
        },{
            header: _('campaigner.bounce.recieved')
            ,dataIndex: 'recieved'
            ,renderer: Ext.util.Format.dateRenderer('d.m.Y')
            //,width: 10
        }]
        // tbar : [{
        //     xtype: 'textfield'
        //     ,name: 'search-details'
        //     ,id: 'campaigner-filter-search'
        //     ,emptyText: _('search')+'...'
        //     ,listeners: {
        //         'change': {fn: this.filterDetails, scope: this}
        //         ,'render': {fn: function(cmp) {
        //             new Ext.KeyMap(cmp.getEl(), {
        //                 key: Ext.EventObject.ENTER
        //                 ,fn: function() {
        //                     this.fireEvent('change',this.getValue());
        //                     this.blur();
        //                     return true;}
        //                 ,scope: cmp
        //             });
        //         },scope:this}
        //     }
        // }]
    });
    Campaigner.grid.StatisticsDetailsBounces.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.grid.StatisticsDetailsBounces,MODx.grid.Grid, {
    _renderActive: function(value, p, rec) {
        if(value == 1) {
            return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" class="small" alt="" />';
        }
        return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" class="small" alt="" />';
    }
//     filterDetails: function(tf,newValue,oldValue) {
//         var nv = newValue;
//         this.getStore().baseParams.search = nv;
//         this.getBottomToolbar().changePage(1);
//         this.refresh();
//         return true;
//     }
});

Ext.reg('campaigner-grid-statistics-details-bounces',Campaigner.grid.StatisticsDetailsBounces);