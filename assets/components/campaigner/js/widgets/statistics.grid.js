Campaigner.grid.Statistics = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url,
        baseParams: { action: 'mgr/statistics/getList', showProcessed: 0 },
        fields: ['id', 'pagetitle', 'subscriber', 'newsletter', 'state', 'date', 'hits', 'sent', 'bounced', 'total', 'priority', 'opened'],
        paging: true,
        autosave: false,
        remoteSort: true,
        primaryKey: 'id',
        sm: this.sm,
        columns: [{
            header: _('campaigner.newsletter')
            ,dataIndex: 'pagetitle'
            ,sortable: true
            ,width: 25
            ,renderer: this._renderNewsletter
        },{
            header: _('campaigner.newsletter.total')
            ,dataIndex: 'total'
            ,sortable: true
            ,width: 10
            ,renderer: this._renderSubscriber
        },{
            header: _('campaigner.statistics.bounced')
            ,dataIndex: 'state'
            ,sortable: true
            ,width: 10
            ,renderer: this._renderState
        },{
            header: _('campaigner.statistics.hits')
            ,dataIndex: 'hits'
            ,sortable: true
            ,width: 10
            ,renderer: this._renderHits
        },{
            header: _('campaigner.statistics.sent')
            ,dataIndex: 'sent'
            ,sortable: true
            ,width: 10
        },{
            header: _('campaigner.statistics.opened')
            ,dataIndex: 'opened'
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
    _renderHits: function(value, p, rec) {
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
    ,showDetails: function(e) {
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
    }

// ,removeQueue: function(e) {
// 	var msg;
// 	if(this.menu.record.state == 0) {
//        msg = _('campaigner.queue.remove.unsend');
//    } else {
//        msg = _('campaigner.queue.remove.confirm');
//    }
//    MODx.msg.confirm({
//     title: _('campaigner.queue.remove.title')
//     ,text: msg
//     ,url: Campaigner.config.connector_url
//     ,params: {
//         action: 'mgr/queue/remove'
//         ,id: this.menu.record.id
//     }
//     ,listeners: {
//         'success': {fn:this.refresh,scope:this}
//     }
// });
// }

});

Campaigner.window.Statistics = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('campaigner.statistics_details') + ' - ' + config.record.pagetitle
        ,width: 750
        ,height: 500
        ,saveBtnText: _('campaigner.okay')
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/statistics/details'
        }
        ,items: [{
            xtype: 'modx-tabs',
            bodyStyle: 'padding: 10px',
            defaults: { border: false ,autoHeight: true },
            border: false,
            items: [
            {
                title: _('campaigner.statistics.open'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-statistics-details-open',
                items: [{
                    html: '<p>'+_('campaigner.statistics.open_info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                // },{
                //     xtype: 'campaigner-grid-statistics-details-open'
                //     // ,fieldLabel: _('campaigner.statistics_details')
                //     ,id: 'campaigner-grid-statistics-details-open'
                //     ,preventRender: true
                // }
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
                    ,preventRender: true
                }]
            },{
                title: _('campaigner.statistics.bounces'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-statistics-details-bounces',
                items: [{
                    html: '<p>'+_('campaigner.statistics.bounces_info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                }]
            },{
                title: _('campaigner.statistics.unsubcribers'),
                defaults: { autoHeight: true },
                id: 'campaigner-tab-statistics-details-unsubscribers',
                items: [{
                    html: '<p>'+_('campaigner.statistics.unsubcribers_info')+'</p>',
                    border: false,
                    bodyStyle: 'padding: 10px'
                }]
            }]
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
            autoScroll: true
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