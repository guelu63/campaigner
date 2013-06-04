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
        ,fields: ['id', 'active', 'email', 'title', 'firstname', 'lastname', 'company', 'type', 'groups', 'key', 'since', 'address']
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
            ,width: 40
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
            header: _('campaigner.subscriber.firstname')
            ,dataIndex: 'firstname'
            ,sortable: true
            ,width: 20
        },{
            header: _('campaigner.subscriber.lastname')
            ,dataIndex: 'lastname'
            ,sortable: true
            ,width: 20
        }
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
            ,menu: {
                items: [
                {
                    text: _('campaigner.subscribers.batch_deactivate')
                    // ,listeners: {
                    //     'click': {fn: this.exportCsv, scope: this}
                    // }
                },{
                    text: _('campaigner.subscribers.batch_remove')
                    // ,listeners: {
                    //     'click': {fn: this.exportXml, scope: this}
                    // }
                }]
            }
        }, '-', '-', '-', {
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
            ,listeners: {
                'click': {fn: this.addSubscriber, scope: this}
            }
            ,hidden: MODx.perm.subscriber_create ? false : true
        }, {
            xtype: 'splitbutton'
            ,text: _('campaigner.subscriber.exports')
            ,menu: {
                items: [
                {
                    text: _('campaigner.subscribers.exportcsv')
                    ,listeners: {
                        'click': {fn: this.exportCsv, scope: this}
                    }
                },{
                    text: _('campaigner.subscribers.exportxml')
                    ,listeners: {
                        'click': {fn: this.exportXml, scope: this}
                    }
                },{
                    text: _('campaigner.subscribers.importcsv')
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
            // ,id: this.menu.record.id
        }
        // ,method: 'remote'
        ,listeners: {
            'success': {fn:function(r) {
                location.href = Campaigner.config.connector_url +'?action=mgr/subscriber/exportxml&export=1&HTTP_MODAUTH=' + MODx.siteId + params;
                // this.refresh();
            },scope:this}
        }
    });
    // window.location.href = Campaigner.config.connector_url +'?action=mgr/subscriber/exportxml&HTTP_MODAUTH=' + Campaigner.site_id + params;
}
,importCsv: function(e) {
    var w = MODx.load({
        xtype: 'campaigner-window-import'
        ,listeners: {
            'success': {fn:this.refresh,scope:this}
        }
    });
    // this.updateWindow.setValues(vals);
    // this.updateWindow.show(e.target);
    // this.on('show',function() { this.fp.getForm().reset(); },this);
    w.show(e.target);
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
// For this to work the processor needs refactoring
// ,verifyPerm: function(perm,rs) {
//     var valid = true;
//     for (var i=0;i<rs.length;i++) {
//         if (rs[i].data.cls.indexOf(perm) == -1) {
//             valid = false;
//         }
//     }
//     return valid;
// }
,getMenu: function() {
    var m = [];
    if (this.getSelectionModel().getCount() == 1) {
        var rs = this.getSelectionModel().getSelections();

        m.push({
            text: _('campaigner.subscriber.edit')
            ,handler: this.editSubscriber
        });
        m.push({
            text: _('campaigner.subscriber.show_statistics')
            ,handler: this.showStatistics
        });
        m.push('-');
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
        // if(this.verifyPerm('remove_subscriber', rs)) {
        if(MODx.perm.remove_subscriber) {
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
            // xtype: 'modx-formpanel'
            // ,id: 'import-form'
            // ,fileUpload: true
            // ,items: [{
            xtype: 'fileuploadfield',
            id: 'form-file',
            name: 'file',
            // buttonOnly: true,
            buttonText: _('campaigner.subscriber.import.select_file'),
            // listeners: {
            //     'fileselected': {fn:function(file) {
            //         var w = Ext.getCmp('my-window-id');
            //         w.submit(true);
            //         // Ext.get('import-form').getForm().submit();
            //         this.analyzeImport(file)
            //     }, scope: this}
            // }
            // }]
        }
        ,{
            xtype: 'textfield'
            ,id: 'delimiter'
            ,name: 'delimiter'
            ,fieldLabel: _('campaigner.subscriber.import.delimiter')
            ,value: ';'
        }
        ,{
            xtype:'fieldset',
            checkboxToggle:true,
            title: _('campaigner.subscriber.import.fieldset_text'),
            autoHeight:true,
            defaults: {width: 210},
            defaultType: 'textfield',
            collapsed: false,
            items: [{
                xtype: 'textfield'
                ,anchor: '100%'
                ,id: 'firstname'
                ,name: 'import[firstname]'
                ,fieldLabel: _('campaigner.subscriber.import.firstname')
                ,value: 'Vorname'
            }
            ,{
                xtype: 'textfield'
                ,anchor: '100%'
                ,id: 'lastname'
                ,name: 'import[lastname]'
                ,fieldLabel: _('campaigner.subscriber.import.lastname')
                ,value: 'Nachname'
            }
            ,{
                xtype: 'textfield'
                ,anchor: '100%'
                ,id: 'email'
                ,name: 'import[email]'
                ,fieldLabel: _('campaigner.subscriber.import.email')
                ,value: 'Email'
            }
            ,{
                xtype: 'textfield'
                ,anchor: '100%'
                ,id: 'active'
                ,name: 'import[active]'
                ,fieldLabel: _('campaigner.subscriber.import.active')
                ,value: 'aktiv'
            }
            ,{
                xtype: 'textfield'
                ,anchor: '100%'
                ,id: 'groups'
                ,name: 'import[groups]'
                ,fieldLabel: _('campaigner.subscriber.import.groups')
                ,value: 'Gruppen'
            }]
        }
        ,{
            xtype: 'checkbox'
            ,id: 'save_file'
            ,name: 'save_file'
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.subscriber.import.save_file')
            ,fieldLabel: _('campaigner.subscriber.import.save_file')
        }]
    });
    Campaigner.window.Import.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.window.Import,MODx.Window);

Ext.extend(Campaigner.window.Import,MODx.Window,{
    analyzeImport: function(file) {
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/subscriber/analyze_import'
                ,file: file.value
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.refresh();
                },scope:this}
            }
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
    // console.log(config.record);
    Ext.applyIf(config,{
        title: _('campaigner.statistics_details') + ' - ' + config.record.email
        ,width: 850
        ,height: 500
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/subscriber/statistics'
        }
        ,items: [{
            title: 'Column Layout - Percentage Only',
            layout:'column',
            items: [{
                    // title: 'Column 1',
                    columnWidth: .33
                },{
                    // title: 'Column 2',
                    columnWidth: .33
                },{
                    // title: 'Column 3',
                    columnWidth: .33
                }]
            },{
                // xtype: 'campaigner-grid-subscriber-statistics'
                // ,fieldLabel: _('campaigner.statistics_details')
                id: 'campaigner-grid-subscriber-statistics'
                ,scope: this
                ,preventRender: true
        }]
    });
    Campaigner.window.SubscriberStatistics.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.window.SubscriberStatistics,MODx.Window);
Ext.reg('campaigner-window-subscriber-statistics',Campaigner.window.SubscriberStatistics);

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
            ,anchor: '100%'
            ,fieldLabel: _('campaigner.subscriber.email')
            ,name: 'email'
            ,id: 'campaigner-'+this.ident+'-email'
        },{
            xtype: 'textfield'
            ,anchor: '100%'
            ,fieldLabel: _('campaigner.subscriber.company')
            ,name: 'company'
            ,id: 'campaigner-'+this.ident+'-company'
        },{
            xtype: 'textfield'
            ,anchor: '100%'
            ,fieldLabel: _('campaigner.subscriber.key')
            ,name: 'key'
            ,id: 'campaigner-'+this.ident+'-key'
            ,width: 250
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
        }, {
         tag: 'div'
         ,cls: 'subscriber-window-groups-header'
         ,html: 'Gruppen'
     }]
 });
Campaigner.window.Subscriber.superclass.constructor.call(this,config);

this.addListener('show', function(cmp) {
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
                this.items.items[0].add({
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
    }, scope: this }
}
});

}, this);
};
Ext.extend(Campaigner.window.Subscriber,MODx.Window);
Ext.reg('campaigner-window-subscriber',Campaigner.window.Subscriber);