// MODx.require('Ext.chart.*');
Campaigner.grid.Subscriber = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    this.gpstore = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({url: Campaigner.config.connector_url, method:'POST'})
        ,baseParams: { action: 'mgr/group/getlist' }
        ,reader: new Ext.data.JsonReader({
            root: 'results'
            ,fields: [ {name: 'id'},{name: 'name'}, {name: 'color'}]
        })
    });
    this.gpstore.load();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/subscriber/getList' }
        ,fields: ['id', 'active', 'email', 'title', 'firstname', 'lastname', 'company', 'type', 'groups', 'key', 'since', 'address', 'street', 'zip', 'city', 'state', 'country', 'import']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        ,sm: this.sm
        ,columns: [
        this.sm,
        {
            header: _('campaigner.subscriber.email')
            ,dataIndex: 'email'
            ,sortable: true
            ,width: 20
        }
        // ,{
        //     header: _('campaigner.subscriber.address')
        //     ,dataIndex: 'address'
        //     ,sortable: true
        //     ,width: 20
        // }
        // ,{
        //     header: _('campaigner.subscriber.title')
        //     ,dataIndex: 'title'
        //     ,sortable: true
        //     ,width: 20
        // }
        ,{
            header: _('campaigner.subscriber.name')
            ,dataIndex: 'firstname'
            ,sortable: true
            ,width: 20
            ,renderer: this._renderName
        }
        // ,{
        //     header: _('campaigner.subscriber.lastname')
        //     ,dataIndex: 'lastname'
        //     ,sortable: true
        //     ,width: 20
        // }
        // ,{
        //     header: _('campaigner.subscriber.company')
        //     ,dataIndex: 'company'
        //     ,sortable: true
        //     ,width: 30
        // }
        ,{
            header: _('campaigner.subscriber.active')
            ,dataIndex: 'active'
            ,sortable: true
            ,width: 10
            ,renderer: this._renderActive
        },{
            header: _('campaigner.subscriber.since')
            ,dataIndex: 'since'
            ,sortable: true
            ,width: 20
            // ,renderer : Ext.util.Format.dateRenderer('Y-m-d')
        },{
            header: _('campaigner.subscriber.type')
            ,dataIndex: 'type'
            ,sortable: true
            ,width: 10
        },{
            header: _('campaigner.subscriber.groups')
            ,dataIndex: 'groups'
            ,sortable: true
            ,width: 10
            ,renderer: this._renderGroups
        }],
        /* Top toolbar */
        tbar : [{
            xtype: 'splitbutton'
            ,text: _('campaigner.subscriber.batch_actions')
            ,hidden: !MODx.perm.subscriber_remove_batch || !MODx.perm.subscriber_togglestatus_batch
            ,menu: {
                items: [
                {
                    text: _('campaigner.subscribers.batch_deactivate')
                    ,hidden: !MODx.perm.subscriber_togglestatus_batch
                    // ,listeners: {
                    //     'click': {fn: this.exportCsv, scope: this}
                    // }
                },{
                    text: _('campaigner.subscribers.batch_remove')
                    ,hidden: !MODx.perm.subscriber_remove_batch
                    // ,listeners: {
                    //     'click': {fn: this.exportXml, scope: this}
                    // }
                }]
            }
        }, {
            xtype: 'combo'
            ,name: 'active'
            ,id: 'campaigner-filter-active'
            ,width: 120
            ,store: [
            ['-', _('campaigner.all')],
            [1, _('campaigner.subscriber.active')],
            [0, _('campaigner.subscriber.inactive')]
            ]
            ,editable: false
            ,triggerAction: 'all'
            ,lastQuery: ''
            ,hiddenName: 'active'
            ,submitValue: false
            ,emptyText: _('campaigner.subscriber.filter.active')
            ,listeners: {
                'change': {fn: this.filterActive, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;
                        }
                        ,scope: cmp
                    });
                },scope:this}
            }
        }, {
            xtype: 'combo'
            ,name: 'type'
            ,id: 'campaigner-filter-type'
            ,width: 120
            ,store: [
            ['-', _('campaigner.all')],
            [1, _('campaigner.subscriber.text')],
            [0, _('campaigner.subscriber.html')]
            ]
            ,editable: false
            ,triggerAction: 'all'
            ,lastQuery: ''
            ,hiddenName: 'type'
            ,submitValue: false
            ,emptyText: _('campaigner.subscriber.filter.type')
            ,listeners: {
                'change': {fn: this.filterType, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;
                        }, scope: cmp
                    });
                },scope:this}
            }
        }, {
            xtype: 'modx-combo'
            ,name: 'group'
            ,id: 'campaigner-filter-group'
            ,width: 120
            ,store: this.gpstore
            ,editable: false
            ,triggerAction: 'all'
            ,lastQuery: ''
            ,hiddenName: 'group'
            ,submitValue: false
            ,emptyText: _('campaigner.subscriber.filter.group')
            ,tpl: new Ext.XTemplate('<tpl for="."><div class="x-combo-list-item"><span style="color: {color};">{name}</span></div></tpl>')
            ,listeners: {
                'change': {fn: this.filterGroup, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;
                        }, scope: cmp
                    });
                },scope:this}
            }
        }, {
            xtype: 'textfield'
            ,name: 'search'
            ,id: 'campaigner-filter-search'
            ,emptyText: _('search')+'...'
            ,listeners: {
                'change': {fn: this.filterSearch, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;
                        }, scope: cmp
                    });
                },scope:this}
            }
        }, '->' , {
            xtype: 'button'
            ,id: 'campaigner-subscriber-add'
            ,text: _('campaigner.subscriber.add')
            ,hidden: !MODx.perm.subscriber_create
            ,listeners: {
                'click': {fn: this.addSubscriber, scope: this}
            }
            ,hidden: !MODx.perm.subscriber_create
        }, {
            xtype: 'splitbutton'
            ,text: _('campaigner.subscriber.exports')
            ,hidden: !MODx.perm.subscriber_export || !MODx.perm.subscriber_import || !MODx.perm.subscriber_export_csv || !MODx.perm.subscriber_export_xml
            ,menu: {
                items: [
                {
                    text: _('campaigner.subscribers.exportcsv')
                    ,hidden: !MODx.perm.subscriber_export_csv
                    ,listeners: {
                        'click': {fn: this.exportCsv, scope: this}
                    }
                },{
                    text: _('campaigner.subscribers.exportxml')
                    ,hidden: !MODx.perm.subscriber_export_xml
                    ,listeners: {
                        'click': {fn: this.exportXml, scope: this}
                    }
                },{
                    text: _('campaigner.subscribers.importcsv')
                    ,hidden: !MODx.perm.subscriber_import
                    ,listeners: {
                        'click': {fn: this.importCsv, scope: this}
                    }
                }]
            }
        }]
    });
Campaigner.grid.Subscriber.superclass.constructor.call(this,config);
};

Ext.extend(Campaigner.grid.Subscriber,MODx.grid.Grid,{
    _renderGroups: function(value, p, rec) {
        var out = '';
        var tip = '';

        if(value) {
            for(var i = 0; i < value.length; i++) {
                if(value[i][2]) {
                    out += '<div class="group" style=" background: '+ value[i][2] +'"></div>';
                    tip += value[i][1] + ' ';
                }
            }
            p.attr = 'ext:qtip="'+ tip +'" ext:qtitle="'+ _('campaigner.groups') +'"';
        }
        return out;
    }
    ,_renderActive: function(value, p, rec) {
        if(value == 1) {
            return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" class="small" alt="" />';
        }
        return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" class="small" alt="" />';
    }
    ,_renderName: function(value, p, rec) {
        // console.log(rec);
        return rec.data.firstname + ' ' + rec.data.lastname;
    }
    ,exportCsv: function() {
        var params = '';
        if(this.getStore().baseParams.text) {
            params += '&text=' + this.getStore().baseParams.text;
        }
        if(this.getStore().baseParams.group) {
            params += '&group=' + this.getStore().baseParams.group;
        }
        if(typeof  this.getStore().baseParams.active != "undefined") {
            params += '&active=' + this.getStore().baseParams.active;
        }
        if(typeof this.getStore().baseParams.search != "undefined") {
            params += '&search=' + this.getStore().baseParams.search;
        }

        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/subscriber/exportcsv'
                // ,id: this.menu.record.id
            }
            // ,method: 'remote'
            ,listeners: {
                'success': {fn:function(r) {
                    location.href = Campaigner.config.connector_url +'?action=mgr/subscriber/exportcsv&export=1&HTTP_MODAUTH=' + MODx.siteId + params;
                    // this.refresh();
                },scope:this}
            }
        });
        // window.location.href = Campaigner.config.connector_url +'?action=mgr/subscriber/exportcsv&HTTP_MODAUTH=' + Campaigner.site_id + params;
    }
    ,exportXml: function() {
        // Collect the params from grid view
        var params = '';
        if(this.getStore().baseParams.text) {
            params += '&text=' + this.getStore().baseParams.text;
        }
        if(this.getStore().baseParams.group) {
            params += '&group=' + this.getStore().baseParams.group;
        }
        if(typeof this.getStore().baseParams.active != "undefined") {
            params += '&active=' + this.getStore().baseParams.active;
        }
        if(typeof this.getStore().baseParams.search != "undefined") {
            params += '&search=' + this.getStore().baseParams.search;
        }

        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/subscriber/exportxml'
            }
            ,listeners: {
                'success': {fn:function(r) {
                    location.href = Campaigner.config.connector_url +'?action=mgr/subscriber/exportxml&export=1&HTTP_MODAUTH=' + MODx.siteId + params;
                },scope:this}
            }
        });
    }
    ,importCsv: function(e) {

        if (!this.updateImportWindow) {
            this.updateImportWindow = MODx.load({
                xtype: 'campaigner-window-import'
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        } else {
            this.updateImportWindow.setValues();
        }
        this.updateImportWindow.show(e.target);

        // var w = MODx.load({
        //     xtype: 'campaigner-window-import'
        //     ,listeners: {
        //         'success': {fn:this.refresh,scope:this}
        //     }
        // });
        // w.reset();
        // this.updateWindow.setValues(vals);
        // this.updateWindow.show(e.target);
        // this.on('show',function() { this.fp.getForm().reset(); },this);
        // w.show(e.target);
    }
    ,filterActive: function(tf,newValue,oldValue) {
        var nv = newValue;
        var s = this.getStore();
        if(nv == '-') {
            delete s.baseParams.active;
        } else {
            s.baseParams.active = nv;
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
    ,filterType: function(tf,newValue,oldValue) {
        var nv = newValue;
        var s = this.getStore();
        if(nv == '-') {
            delete s.baseParams.text;
        } else {
            s.baseParams.text = nv;
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
    ,filterGroup: function(tf,newValue,oldValue) {
        var nv = newValue;
        var s = this.getStore();
        if(nv == '-') {
            delete s.baseParams.group;
        } else {
            s.baseParams.group = nv;
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
    ,filterSearch: function(tf,newValue,oldValue) {
        var nv = newValue;
        this.getStore().baseParams.search = nv;
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
    ,addSubscriber: function(e) {
        var w = MODx.load({
            xtype: 'campaigner-window-subscriber'
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        w.show(e.target);
    }
    ,editSubscriber: function(e) {
        this.updateWindow = MODx.load({
            xtype: 'campaigner-window-subscriber'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        var vals = this.menu.record;
        vals.text = vals.type == 'text' ? 1 : 0;
        this.updateWindow.setValues(vals);
        this.updateWindow.show(e.target);
    }
    ,activateSubscriber: function() {
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/subscriber/activate'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.refresh();
                },scope:this}
            }
        });
    }
    ,deactivateSubscriber: function() {
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/subscriber/deactivate'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.refresh();
                },scope:this}
            }
        });
    }
    ,removeSubscriber: function() {
        MODx.msg.confirm({
            title: _('campaigner.subscriber.remove.title')
            ,text: _('campaigner.subscriber.remove.confirm')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/subscriber/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.refresh();
                },scope:this}
            }
        });
    }
    ,showStatistics: function(e) {
        this.updateWindow = MODx.load({
            xtype: 'campaigner-window-subscriber-statistics'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        var vals = this.menu.record;
        vals.text = vals.type == 'text' ? 1 : 0;
        this.updateWindow.setValues(vals);
        this.updateWindow.show(e.target);
    }
    ,getMenu: function() {
        var m = [];
        if (this.getSelectionModel().getCount() == 1) {
            var rs = this.getSelectionModel().getSelections();

            if(MODx.perm.subscriber_edit) {
                m.push({
                    text: _('campaigner.subscriber.edit')
                    ,handler: this.editSubscriber
                });
            }
            if(MODx.perm.subscriber_showstats) {
                m.push({
                    text: _('campaigner.subscriber.show_statistics')
                    ,handler: this.showStatistics
                });
                m.push('-');
            }
            if(MODx.perm.subscriber_togglestatus) {
                if(this.menu.record.active != 1) {
                    m.push({
                        text: _('campaigner.subscriber.activate')
                        ,handler: this.activateSubscriber
                    });
                } else {
                    m.push({
                        text: _('campaigner.subscriber.deactivate')
                        ,handler: this.deactivateSubscriber
                    });
                }
            }
            if(MODx.perm.subscriber_remove) {
                m.push('-');
                m.push({
                    text: _('campaigner.subscriber.remove')
                    ,handler: this.removeSubscriber
                });
            }
        }
        if (m.length > 0) {
            this.addContextMenuItem(m);
        }
    }
});
Ext.reg('campaigner-grid-subscriber',Campaigner.grid.Subscriber);

/**
 * Import subscribers
 */
Campaigner.window.Import = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('campaigner.subscriber.import')
        ,closeAction: 'hide'
        ,width: 500
        ,height: 500
        ,padding: '10px'
        ,saveBtnText: _('campaigner.subscriber.import.button_text')
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/subscriber/make_import'
        }
        ,fileUpload: true
        ,fields: [{
            xtype: 'modx-combo-browser'
            ,anchor: '100%'
            ,description: _('campaigner.subscriber.import.select_file.description')
            ,openTo: 'assets/components/campaigner/imports/'
            ,allowedFileTypes: 'csv,xml'
            ,hideFiles: true
            ,id: 'form-file'
            ,name: 'file'
            ,fieldLabel: _('campaigner.subscriber.import.select_file')
            ,listeners: {
                'select': {fn: function(data) {
                    this.analyzeImport(data.fullRelativeUrl);
                },scope:this}
            }
        }
        ,{
            xtype: 'textfield'
            ,id: 'delimiter'
            ,name: 'delimiter'
            ,fieldLabel: _('campaigner.subscriber.import.delimiter')
            ,value: ';'
        }
        ,{
            xtype:'fieldset'
            ,checkboxToggle:true
            ,title: _('campaigner.subscriber.import.fieldset_text')
            ,collapsed: false
            ,items: [{
                layout: 'column'
                ,items: [{
                    columnWidth: .5
                    ,layout: 'form'
                    ,items: [{
                        xtype: 'textfield'
                        ,anchor: '100%'
                        ,id: 'address'
                        ,name: 'import[address]'
                        ,fieldLabel: _('campaigner.subscriber.import.address')
                        ,value: 'Anrede'
                    },{
                        xtype: 'textfield'
                        ,anchor: '100%'
                        ,id: 'firstname'
                        ,name: 'import[firstname]'
                        ,fieldLabel: _('campaigner.subscriber.import.firstname')
                        ,value: 'Vorname'
                    },{
                        xtype: 'textfield'
                        ,anchor: '100%'
                        ,id: 'email'
                        ,name: 'import[email]'
                        ,fieldLabel: _('campaigner.subscriber.import.email')
                        ,value: 'Email'
                    },{
                        xtype: 'textfield'
                        ,anchor: '100%'
                        ,id: 'groups'
                        ,name: 'import[groups]'
                        ,fieldLabel: _('campaigner.subscriber.import.groups')
                        ,value: 'Gruppen'
                    }]
                },{
                    columnWidth: .5
                    ,layout: 'form'
                    ,items: [{
                        xtype: 'textfield'
                        ,anchor: '100%'
                        ,id: 'title'
                        ,name: 'import[title]'
                        ,fieldLabel: _('campaigner.subscriber.import.title')
                        ,value: 'Titel'
                    },{
                        xtype: 'textfield'
                        ,anchor: '100%'
                        ,id: 'lastname'
                        ,name: 'import[lastname]'
                        ,fieldLabel: _('campaigner.subscriber.import.lastname')
                        ,value: 'Nachname'
                    },{
                        xtype: 'textfield'
                        ,anchor: '100%'
                        ,id: 'active'
                        ,name: 'import[active]'
                        ,fieldLabel: _('campaigner.subscriber.import.active')
                        ,value: 'aktiv'
                    }]
                }]
            }]
        }
        ,{
            layout: 'column'
            ,items: [{
                columnWidth: .5
                ,layout: 'form'
                ,items: [{
                    xtype: 'checkbox'
                    ,id: 'default_group'
                    ,name: 'default_group'
                    ,labelSeparator: ''
                    ,hideLabel: true
                    ,boxLabel: _('campaigner.subscriber.import.default_group')
                    ,fieldLabel: _('campaigner.subscriber.import.default_group')
                }]
            },{
                columnWidth: .5
                ,layout: 'form'
                ,items: [{
                    xtype: 'checkbox'
                    ,id: 'save_file'
                    ,name: 'save_file'
                    ,labelSeparator: ''
                    ,hideLabel: true
                    ,boxLabel: _('campaigner.subscriber.import.save_file')
                    ,fieldLabel: _('campaigner.subscriber.import.save_file')
                }]
            }]
        },{
            xtype: 'textfield'
            ,anchor: '100%'
            ,id: 'test'
            ,name: 'test'
            ,fieldLabel: _('campaigner.subscriber.import.test')
        }]
    });
    Campaigner.window.Import.superclass.constructor.call(this,config);
}

Ext.extend(Campaigner.window.Import,MODx.Window,{
    analyzeImport: function(file) {
        // var success = false;
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/subscriber/analyze_import'
                ,file: file
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.returnMsg(r);
                },scope:this}
                ,'failure': {fn:function(r) {
                    this.returnMsg(r);
                },scope:this}
            }
        });
        // console.log(success);
        // return success;
    }
    ,returnMsg: function(response) {

        // p = response.object;
        // for (var key in p) {
        //     if (p.hasOwnProperty(key)) {
        //         alert(key + " -> " + p[key]);
        //     }
        // }
        console.log(response.object);
        var tpl = new Ext.XTemplate(
            '<tpl for=".">',
                '<p>{key}</p>',
                '<span>{value}',
            '</tpl>'
        );

        // Ext.widget('panel', {
        //     renderTo: Ext.get('campaigner-subscriber-import-status')
        //     // 4 is the number of indentation spaces
        //     ,tpl: '<pre>{[JSON.stringify(values, null, 4)]}</pre>'
        //     ,height: 400
        //     ,data: response.object // sample data
        // });

        // var content = Ext.DataView({
        //     // itemSelector : 'div.basket-fileblock',    //Required
        //     // style : 'overflow:auto',
        //     // multiSelect : true,
        //     store : response.object
        //     ,tpl : tpl
        //     ,renderTo: Ext.get('campaigner-subscriber-import-status')
        // });
        // console.log(content);
        MODx.msg.status({
            id: 'campaigner-subscriber-import-status'
            ,title: _('campaigner.subscriber.import.analyze')
            ,message: response.message + '<br/></br/>' + tpl.apply(Ext.encode(response.object))
            ,delay: 5
        });
    }
});

Ext.reg('campaigner-window-import',Campaigner.window.Import);

/**
 * Subscriber statistics
 * @todo  Build it!
 */
Campaigner.window.SubscriberStatistics = function(config) {
    config = config || {};
    Ext.chart.Chart.CHART_URL = 'http://dev.sencha.com/deploy/ext-3.4.0/resources/charts.swf';
    var stats = new Ext.data.JsonStore({
        proxy: new Ext.data.HttpProxy({
            url: Campaigner.config.connector_url
            ,method:'POST'
            // ,action: 'mgr/subscriber/getcumulatedstats'
        })
        ,baseParams: {
            action: 'mgr/subscriber/getcumulatedstats'
            ,subscriber: config.record.id
        }
        // ,reader: new Ext.data.JsonReader({
        ,root: 'results'
            // ,fields: [ {name: 'view_total'},{name: 'views_unique'}, {name: 'clicks_total'}, {name: 'clicks_unique'}]
        ,fields: ['opens', 'clicks', 'newsletter']
        // ,totalProperty: 'total'
        ,idProperty: 'id'
        ,remoteSort: true
        // })
    });
    stats.load();

    Ext.applyIf(config,{
        title: _('campaigner.statistics_details') + ' - ' + config.record.email
        ,width: 850
        ,height: 500
        // ,url: Campaigner.config.connectorUrl
        // ,baseParams: {
        //     action: 'mgr/subscriber/statistics'
        // }
        ,items: [{
            xtype: 'stackedbarchart',
            height: 300,
            store: stats,
            yField: 'newsletter',
            xAxis: new Ext.chart.NumericAxis({
                stackingEnabled: true,
                // labelRenderer: Ext.util.Format.usMoney
            }),
            series: [{
                xField: 'clicks',
                displayName: _('campaigner.statistics.clicks')
            },{
                xField: 'opens',
                displayName: _('campaigner.statistics.opens')
            }]
            ,tipRenderer : function(chart, record){
                return Ext.util.Format.number(record.data.visits, '0,0') + ' visits in ' + record.data.name;
            }
            ,minorTickSteps: 1
            ,majorTickSteps: 1
            // xtype: 'linechart'
            // ,store: stats
            // ,xField: 'newsletter'
            // ,yField: 'opens'
            // ,tipRenderer : function(chart, record){
            //     return record.data.opens + ' ' + _('campaigner.statistics.opens') + record.data.newsletter;
            // }
        },{
            xtype: 'campaigner-grid-subscriber-statistics'
            // ,fieldLabel: _('campaigner.statistics_details')
            ,id: 'campaigner-grid-subscriber-statistics'
            ,scope: this
            ,bodyStyle: 'padding: 10px'
            ,preventRender: true
            // ,baseParams: {
            //     action: 'mgr/subscriber/getsinglestats'
            //     ,subscriber: config.record.id
            // }
        }]
    });
    Campaigner.window.SubscriberStatistics.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.window.SubscriberStatistics,MODx.Window
    ,{
        constructor: function(cfg) {
            this.initConfig(cfg);
        }
    }
);
Ext.reg('campaigner-window-subscriber-statistics',Campaigner.window.SubscriberStatistics);

Campaigner.grid.SubscriberStatistics = function(config) {
    config = config || {};

    var types = new Ext.data.JsonStore({
        proxy: new Ext.data.HttpProxy({
            url: Campaigner.config.connector_url
            ,method:'POST'
            // ,action: 'mgr/subscriber/getcumulatedstats'
        })
        ,baseParams: {
            action: 'mgr/statistics/getclicktypes'
        }
        // ,reader: new Ext.data.JsonReader({
        ,root: 'results'
            // ,fields: [ {name: 'view_total'},{name: 'views_unique'}, {name: 'clicks_total'}, {name: 'clicks_unique'}]
        ,fields: [
            {name: 'name', type: 'string'},
            {name: 'value', type: 'string'}
        ]
        // ,totalProperty: 'total'
        ,idProperty: 'id'
        ,remoteSort: true
        // })
    });
    types.load();
    
    // types.add({name: _('campaigner.all'), 'value': ''});
    // this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        // id: 'campaigner-grid-subscriber-statistics'
        url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/subscriber/getsinglestats'
            ,subscriber: config.scope.record.id
        }
        ,viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoScroll: true,
            emptyText: _('campaigner.grid.no_data')
        }
        ,primaryKey: 'id'
        // ,sm: this.sm
        ,fields: ['id','link', 'hit_date', 'view_total', 'client', 'ip', 'newsletter', 'hit_type']
        ,paging: true
        ,pageSize: 10
        ,trackMouseOver:true
        ,remoteSort: true
        ,columns: [{
            header: _('campaigner.statistics.link')
            ,dataIndex: 'link'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.newsletter')
            ,dataIndex: 'newsletter'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.statistics.hit_type')
            ,dataIndex: 'hit_type'
            ,sortable: true
            ,width: 15
            ,renderer: this._renderHittype
        },{
            header: _('campaigner.statistics.hit_date')
            ,dataIndex: 'hit_date'
            ,sortable: true
            ,width: 15
            ,renderer: Ext.util.Format.dateRenderer('d.m.Y H:i')
        },{
            header: _('campaigner.statistics.view_total')
            ,dataIndex: 'view_total'
            ,sortable: true
            ,width: 5
        },{
            header: _('campaigner.statistics.client')
            ,dataIndex: 'client'
            ,sortable: true
            ,width: 25
        },{
            header: _('campaigner.statistics.ip')
            ,dataIndex: 'ip'
            ,sortable: true
            ,width: 15
        }],
        tbar : [{
            xtype: 'modx-combo'
            ,name: 'hittype'
            ,id: 'campaigner-filter-hittype'
            ,triggerAction: 'all'
            ,lastQuery: ''
            ,emptyText: _('campaigner.all')
            ,hiddenName: 'hittype'
            ,displayField: 'name'
            ,valueField: 'value'
            // ,store: [
            //     ['-', _('campaigner.all')],
            //     ['click', _('campaigner.statistics.type.click')],
            //     ['image', _('campaigner.statistics.type.open')],
            //     ['facebook', _('campaigner.statistics.type.facebook')],
            //     ['twitter', _('campaigner.statistics.type.twitter')],
            //     ['google', _('campaigner.statistics.type.google')],
            // ]
            ,store: types
            ,submitValue: false
            ,listeners: {
                'change': {fn: this.filterHitType, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;
                        }
                        ,scope: cmp
                    });
                },scope:this}
            }
        }
        ,{
            xtype: 'datefield'
            ,name: 'date_from'
            ,id: 'campaigner-filter-date-from'
            ,triggerAction: 'all'
            ,lastQuery: ''
            ,hiddenName: 'date_from'
            ,submitValue: false
            ,format: 'Y-m-d'
            ,listeners: {
                'change': {fn: this.filterDateFrom, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;
                        }
                        ,scope: cmp
                    });
                },scope:this}
            }
        }
        ,{
            xtype: 'datefield'
            ,name: 'date_to'
            ,id: 'campaigner-filter-date-to'
            ,triggerAction: 'all'
            ,lastQuery: ''
            ,hiddenName: 'date_to'
            ,submitValue: false
            ,format: 'Y-m-d'
            ,listeners: {
                'change': {fn: this.filterDateTo, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;
                        }
                        ,scope: cmp
                    });
                },scope:this}
            }
        }, '->'
        ,{
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
        }
        ,{
            xtype: 'button'
            ,text: _('campaigner.statistics.export')
            ,disabled: !MODx.perm.statistics_opens_export
            ,handler: this.exportData
        }]
    });
    Campaigner.grid.SubscriberStatistics.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.grid.SubscriberStatistics,MODx.grid.Grid
    ,{
        constructor: function(cfg) {
            this.initConfig(cfg);
        }
        ,_renderHittype: function(value, p, rec) {
            return _('campaigner.statistics.' + value);
        }
        ,filterHitType: function(tf,newValue,oldValue) {
            var nv = newValue;
            var s = this.getStore();
            console.log(s);
            if(nv == '-') {
                delete s.baseParams.hittype;
            } else {
                s.baseParams.hittype = nv;
            }
            this.getBottomToolbar().changePage(1);
            this.refresh();
            return true;
        }
        ,filterDateFrom: function(tf,newValue,oldValue) {
            var nv = newValue;
            var s = this.getStore();
            console.log(s);
            if(nv == '-') {
                delete s.baseParams.date_from;
            } else {
                s.baseParams.date_from = Ext.util.Format.date(nv, 'Y-m-d H:i:s');
            }
            this.getBottomToolbar().changePage(1);
            this.refresh();
            return true;
        }
        ,filterDateTo: function(tf,newValue,oldValue) {
            var nv = newValue;
            var s = this.getStore();
            if(nv == '-') {
                delete s.baseParams.date_to;
            } else {
                s.baseParams.date_to = Ext.util.Format.date(nv, 'Y-m-d H:i:s');;
            }
            this.getBottomToolbar().changePage(1);
            this.refresh();
            return true;
        }
    }

);
Ext.reg('campaigner-grid-subscriber-statistics', Campaigner.grid.SubscriberStatistics);

// Ext.onReady(function() {
//     var stats = new Ext.data.Store({
//         proxy: new Ext.data.HttpProxy({url: Campaigner.config.connector_url, method:'POST'})
//         ,baseParams: {
//             action: 'mgr/subscriber/getcumulatedstats'
//         }
//         ,reader: new Ext.data.JsonReader({
//             root: 'results'
//             ,fields: [ {name: 'views_total'},{name: 'views_unique'}, {name: 'clicks_total'}, {name: 'clicks_unique'}]
//         })
//     });
//     // this.sub_stats_store.load();

//     var chart = new Ext.chart.Chart({
//         style: 'background:#fff'
//         ,animate: true
//         ,shadow: true
//         ,store: stats
//         ,axes: [{
//             type: 'Numeric',
//             position: 'left',
//             fields: ['data1'],
//             label: {
//                 renderer: Ext.util.Format.numberRenderer('0,0')
//             },
//             title: 'Number of Hits',
//             grid: true,
//             minimum: 0
//         }, {
//             type: 'Category',
//             position: 'bottom',
//             fields: ['name'],
//             title: 'Month of the Year'
//         }]
//     });
//     chart.render('body');
// });

Campaigner.window.Subscriber = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    this.gpstore = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({url: Campaigner.config.connector_url, method:'POST'})
        ,baseParams: { action: 'mgr/group/getlist' }
        ,reader: new Ext.data.JsonReader({
            root: 'results',
            fields: [ {name: 'id'},{name: 'name'}, {name: 'color'}]
        })
    });
    Ext.applyIf(config,{
        title: config.hasOwnProperty('record') ? _('campaigner.subscriber.edit') : _('campaigner.subscriber.add')
        ,id: this.ident
        ,height: 400
        ,width: 475
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/subscriber/save'
        ,defaults: {
            labelAlign: 'right'
            ,labelPad: 10
            ,boxMinWidth: 350
        }
        ,fields: [{
            xtype: 'modx-tabs',
            autoHeight: true,
            deferredRender: false,
            forceLayout: true,
            width: '98%',
            bodyStyle: 'padding: 10px 10px 10px 10px;',
            border: true,
            defaults: {
                border: false,
                autoHeight: true,
                bodyStyle: 'padding: 5px 8px 5px 5px;',
                layout: 'form',
                deferredRender: false,
                forceLayout: true
            },
            items: [{
                title: _('campaigner.subscriber.tab.main')
                ,items: [{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,readOnly: true
                    ,hidden: true
                    ,name: 'id'
                    ,id: this.ident +'-id'
                },{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.address')
                    ,name: 'address'
                    ,id: 'campaigner-'+this.ident+'-address'
                },{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.title')
                    ,name: 'title'
                    ,id: 'campaigner-'+this.ident+'-title'
                },{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.firstname')
                    ,name: 'firstname'
                    ,id: 'campaigner-'+this.ident+'-firstname'
                },{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.lastname')
                    ,name: 'lastname'
                    ,id: 'campaigner-'+this.ident+'-lastname'
                },{
                    xtype: 'textfield'
                    ,vtype: 'email'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.email')
                    ,name: 'email'
                    ,id: 'campaigner-'+this.ident+'-email'
                },{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.key')
                    ,name: 'key'
                    ,id: 'campaigner-'+this.ident+'-key'
                },{
                    xtype: 'xdatetime'
                    ,fieldLabel: _('campaigner.subscriber.since')
                    ,name: 'since'
                    ,id: 'campaigner-'+this.ident+'-since'
                    ,dateFormat: 'Y-m-d'
                    ,timeFormat: 'H:i'
                    ,dateWidth: 120
                    ,timeWidth: 120
                },{
                    xtype: 'checkbox'
                    ,fieldLabel: _('campaigner.subscriber.active')
                    ,name: 'active'
                    ,id: 'campaigner-'+this.ident+'-active'
                    ,labelSeparator: ''
                    ,hideLabel: true
                    ,boxLabel: _('campaigner.subscriber.active')
                },{
                    xtype: 'checkbox'
                    ,fieldLabel: _('campaigner.subscriber.astext')
                    ,name: 'text'
                    ,id: 'campaigner-'+this.ident+'-astext'
                    ,labelSeparator: ''
                    ,hideLabel: true
                    ,boxLabel: _('campaigner.subscriber.astext')
                },{
                    xtype: 'checkbox'
                    ,fieldLabel: _('campaigner.subscriber.imported')
                    ,name: 'import'
                    ,id: 'campaigner-'+this.ident+'-imported'
                    ,labelSeparator: ''
                    ,hideLabel: true
                    ,boxLabel: _('campaigner.subscriber.imported')
                }]
            },{
                title: _('campaigner.subscriber.tab.address')
                ,items: [{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.company')
                    ,name: 'company'
                    ,id: 'campaigner-'+this.ident+'-company'
                },{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.street')
                    ,name: 'street'
                    ,id: 'campaigner-'+this.ident+'-street'
                },{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.zip')
                    ,name: 'zip'
                    ,id: 'campaigner-'+this.ident+'-zip'
                },{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.city')
                    ,name: 'city'
                    ,id: 'campaigner-'+this.ident+'-city'
                },{
                    xtype: 'textfield'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.state')
                    ,name: 'state'
                    ,id: 'campaigner-'+this.ident+'-state'
                },{
                    xtype: 'modx-combo-country'
                    ,anchor: '100%'
                    ,fieldLabel: _('campaigner.subscriber.country')
                    ,name: 'country'
                    ,id: 'campaigner-'+this.ident+'-country'
                }]
            }
            ,{
                title: _('campaigner.subscriber.tab.groups')
                ,id: 'campaigner-subscriber-'+this.ident+'-groups'
                ,xtype: 'campaigner-subscriber-groups'
                ,scope: this
            }
            ,{
                title: _('campaigner.subscriber.tab.fields')
                ,id: 'campaigner-subscriber-'+this.ident+'-fields'
                ,xtype: 'campaigner-subscriber-fields'
                ,scope: this
            }]
        }]
    });
    Campaigner.window.Subscriber.superclass.constructor.call(this,config);
};

Ext.extend(Campaigner.window.Subscriber,MODx.Window);
Ext.reg('campaigner-window-subscriber',Campaigner.window.Subscriber);

Campaigner.panel.SubscriberGroups = function (config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    var id = null;
    var record = config.scope.record;
    Ext.Ajax.request({
        url: Campaigner.config.connector_url
        ,params: {
            action: 'mgr/group/getgrouplist'
            ,subscriber: record.id
        }
        ,scope: this
        // ,listeners: {
            ,success: function(response) {
                var groups = Ext.util.JSON.decode(response.responseText);
                var checked = false;
                groups = groups.object;
                if(groups.length > 0) {
                    Ext.each(groups, function(item, key) {

                        checked = false;
                        if(record.groups) {
                            Ext.each(record.groups, function(i, k) {
                                if(item.id == i[0]) checked = true;
                            });
                        }
                        this.add({
                            xtype: 'checkbox'
                            ,name: 'groups[]'
                            ,fieldLabel: item.name
                            ,inputValue: item.id
                            ,checked: checked
                            ,labelSeparator: ''
                            ,width: '45%'
                            ,hideLabel: true
                            ,boxLabel: '<span style="color: ' + item.color + ';">' + item.name + '</span>'
                        });
                    }, this);
                }
                this.doLayout(false, true);
            }
            // , scope: this }
        // }
    });
    Campaigner.panel.SubscriberGroups.superclass.constructor.call(this, config);
};
Ext.extend(Campaigner.panel.SubscriberGroups, Ext.Container
    ,{
        constructor: function(cfg) {
            this.initConfig(cfg);
        }
    }
);
Ext.reg('campaigner-subscriber-groups', Campaigner.panel.SubscriberGroups);


/**
* Subscriber Fields
*
* @class Campaigner.Panel.Fields
* @extends Ext.Container
* @xtype modx-subscriber-fields
*/
Campaigner.panel.SubscriberFields = function (config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    var id = null;
    var record = config.scope.record;

    MODx.Ajax.request({
        url: Campaigner.config.connector_url
        ,params: {
            action: 'mgr/subscriber/getsubscriberfields'
            ,subscriber: record.id
        }
        ,scope: this
        ,listeners: {
            'success': {fn: function(response) {
                var fields = Ext.decode(response.responseText);
                var checked = false;
                fields = response.object;

                if(fields.length > 0) {
                    Ext.each(fields, function(item, key) {
                        var config = {
                            xtype: item.type
                            ,name: 'fields[' + item.id + ']'
                            ,id: 'campaigner-'+this.ident+'-field-' + item.name
                            ,fieldLabel: item.label
                            ,anchor: '100%'
                        };
                        switch(item.type) {
                            case "textfield":
                                config = Ext.apply({
                                    value: item.value
                                }, config);
                            break;

                            case "modx-combo":
                                config = Ext.apply({
                                    displayField: 'name'
                                    ,valueField: 'id'
                                    ,store: new Ext.data.JsonStore({
                                        url: Campaigner.config.connector_url
                                        ,baseParams: {
                                            action : 'mgr/fields/getFieldValues'
                                            ,key: item.id
                                        }
                                        ,fields: ['id','name']
                                        ,root: 'results'

                                        // ,data: item.values
                                    })
                                    ,mode: 'remote'
                                }, config);
                            break;

                            case "textarea":
                                config = Ext.apply({
                                    value: item.value
                                }, config);
                            break;

                            case "datefield":
                                config = Ext.apply({
                                    dateFormat: item.format
                                    ,timeFormat: 'H:i'
                                    ,dateWidth: 120
                                    ,timeWidth: 120
                                    ,value: item.value
                                }, config);
                            break;

                            case "radiogroup":

                            break;

                            // case "checkboxgroup":
                            //     values = new Array();
                            //     var store = new Ext.data.JsonStore({
                            //         url: Campaigner.config.connector_url
                            //         ,baseParams: {
                            //             action : 'mgr/fields/getFieldValues'
                            //             ,key: item.id
                            //         }
                            //         ,root: 'results'
                            //         ,fields: ['id','name']
                            //         ,autoLoad: true
                            //         ,listeners: {
                            //             load: function(t, records, options) {
                            //                 for (var i=0; i<records.length; i++) {
                            //                     values.push({name: "myfield[]", inputValue: records[i].data.id, boxLabel: records[i].data.name});
                            //                 }
                            //             }
                            //         }
                            //     });
                            //     console.log(store);
                            //     config = Ext.apply({
                            //         // Distribute controls across 3 even columns, filling each row
                            //         // from left to right before starting the next row
                            //         columns: 3,
                            //         items: values
                            //     }, config);
                            // break;
                        }
                        // checked = false;
                        // if(cmp.record.fields) {
                        //     Ext.each(cmp.record.fields, function(i, k) {
                        //         if(item.id == i[0]) checked = true;
                        //     });
                        // }
                        // var values = null;
                        // if(item.values)
                        //     values = item.values;
                        // console.log(item.values);
                        this.add(config);
                    }, this);
                }
                this.doLayout(false, true);
            }, scope: this }
        }
    });
    Campaigner.panel.SubscriberFields.superclass.constructor.call(this, config);
};
Ext.extend(Campaigner.panel.SubscriberFields, Ext.Container, {
    constructor: function(cfg) {
        this.initConfig(cfg);
    }
});
Ext.reg('campaigner-subscriber-fields', Campaigner.panel.SubscriberFields);

/**
 * @todo  Make custom xtype from this

this.addListener('show', function(cmp) {
    var fieldcontainer = Ext.getCmp('campaigner-'+this.ident+'-fields');
    var id = null;
    // if(cmp.record) id = cmp.record.id;
    MODx.Ajax.request({
        url: Campaigner.config.connector_url
        ,params: {
            action: 'mgr/subscriber/getsubscriberfields'
            ,subscriber: id
        }
        ,scope: this
        ,listeners: {
            'success': {fn: function(response) {
                var fields = Ext.decode(response.responseText);
                var checked = false;
                fields = response.object;

                if(fields.length > 0) {
                    Ext.each(fields, function(item, key) {
                        var config = {
                            xtype: item.type
                            ,name: 'fields[' + item.id + ']'
                            ,id: 'campaigner-'+this.ident+'-field-' + item.name
                            ,fieldLabel: item.label
                            ,anchor: '100%'
                        };
                        switch(item.type) {
                            case "textfield":
                                config = Ext.apply({
                                    value: item.value
                                }, config);
                            break;

                            case "modx-combo":
                                config = Ext.apply({
                                    displayField: 'name'
                                    ,valueField: 'id'
                                    ,store: new Ext.data.JsonStore({
                                        url: Campaigner.config.connector_url
                                        ,baseParams: {
                                            action : 'mgr/fields/getFieldValues'
                                            ,key: item.id
                                        }
                                        ,fields: ['id','name']
                                        ,root: 'results'

                                        // ,data: item.values
                                    })
                                    ,mode: 'remote'
                                }, config);
                            break;

                            case "textarea":
                                config = Ext.apply({
                                    value: item.value
                                }, config);
                            break;

                            case "datefield":
                                config = Ext.apply({
                                    dateFormat: item.format
                                    ,timeFormat: 'H:i'
                                    ,dateWidth: 120
                                    ,timeWidth: 120
                                    ,value: item.value
                                }, config);
                            break;

                            case "radiogroup":

                            break;

                            // case "checkboxgroup":
                            //     values = new Array();
                            //     var store = new Ext.data.JsonStore({
                            //         url: Campaigner.config.connector_url
                            //         ,baseParams: {
                            //             action : 'mgr/fields/getFieldValues'
                            //             ,key: item.id
                            //         }
                            //         ,root: 'results'
                            //         ,fields: ['id','name']
                            //         ,autoLoad: true
                            //         ,listeners: {
                            //             load: function(t, records, options) {
                            //                 for (var i=0; i<records.length; i++) {
                            //                     values.push({name: "myfield[]", inputValue: records[i].data.id, boxLabel: records[i].data.name});
                            //                 }
                            //             }
                            //         }
                            //     });
                            //     console.log(store);
                            //     config = Ext.apply({
                            //         // Distribute controls across 3 even columns, filling each row
                            //         // from left to right before starting the next row
                            //         columns: 3,
                            //         items: values
                            //     }, config);
                            // break;
                        }
                        // checked = false;
                        // if(cmp.record.fields) {
                        //     Ext.each(cmp.record.fields, function(i, k) {
                        //         if(item.id == i[0]) checked = true;
                        //     });
                        // }
                        // var values = null;
                        // if(item.values)
                        //     values = item.values;
                        // console.log(item.values);
                        fieldcontainer.add(config);
                    }, this);
                }
                fieldcontainer.doLayout(false, true);
            }, scope: this }
        }
    });
}, this);
*/
/**
 * @todo  Make custom xtype from this

this.addListener('show', function(cmp) {
    var groupcontainer = Ext.getCmp('campaigner-'+this.ident+'-groups');
    console.log(groupcontainer);
    var id = null;
    if(cmp.record) id = cmp.record.id;
    MODx.Ajax.request({
        url: Campaigner.config.connector_url
        ,params: {
            action: 'mgr/group/getSubscriberList'
            ,subscriber: id
        }
        ,scope: this
        ,listeners: {
            'success': {fn: function(response) {
                var groups = Ext.decode(response.responseText);
                var checked = false;
                groups = response.object;

                if(groups.length > 0) {
                    Ext.each(groups, function(item, key) {
                        checked = false;
                        if(cmp.record.groups) {
                            Ext.each(cmp.record.groups, function(i, k) {
                                if(item.id == i[0]) checked = true;
                            });
                        }
                        groupcontainer.add({
                            xtype: 'checkbox'
                            ,name: 'groups[]'
                            ,fieldLabel: item.name
                            ,inputValue: item.id
                            ,checked: checked
                            ,labelSeparator: ''
                            ,width: '45%'
                            ,hideLabel: true
                            ,boxLabel: '<span style="color: ' + item.color + ';">' + item.name + '</span>'
                        });
                    }, this);
                }
                groupcontainer.doLayout(false, true);
            }, scope: this }
        }
    });
}, this);
*/