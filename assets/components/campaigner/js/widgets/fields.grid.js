Campaigner.grid.Fields = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/fields/getList' }
        ,fields: ['id', 'name', 'label', 'type', 'required', 'active', 'values', 'format']
        ,paging: true
        ,remoteSort: true
        ,enableDragDrop: true
        ,ddGroup: 'dd'
        ,listeners: {
            "render": {
                scope: this,
                fn: function(grid) {
                // Enable sorting Rows via Drag & Drop
                // this drop target listens for a row drop
                // and handles rearranging the rows

                    var ddrow = new Ext.dd.DropTarget(grid.container, {
                        ddGroup : 'dd',
                        copy:false,
                        notifyDrop : function(dd, e, data){

                            var ds = grid.store;

                            // NOTE:
                            // you may need to make an ajax call
                            // here
                            // to send the new order
                            // and then reload the store
                            console.log(ds);
                            // MODx.Ajax.request({
                            //     url: Campaigner.config.connector_url
                            //     ,params: {
                            //         action: 'mgr/fields/updateOrder'
                            //         ,field: id
                            //     }
                            // })

                            // alternatively, you can handle the
                            // changes
                            // in the order of the row as
                            // demonstrated below

                            // ***************************************

                            var sm = grid.getSelectionModel();
                            var rows = sm.getSelections();
                            if(dd.getDragData(e)) {
                                var cindex=dd.getDragData(e).rowIndex;
                                if(typeof(cindex) != "undefined") {
                                    for(i = 0; i <  rows.length; i++) {
                                    ds.remove(ds.getById(rows[i].id));
                                    }
                                    ds.insert(cindex,data.selections);
                                    sm.clearSelections();
                                }
                            }
                        }
                    })
                    // load the grid store
                    // after the grid has been rendered
                    this.store.load();
                }
            }
        }
        // ,primaryKey: 'id'
        // ,save_action: 'mgr/socialsharing/updateFromGrid'
        // ,autosave: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 3
        },{
            header: _('campaigner.fields.name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.fields.label')
            ,dataIndex: 'label'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.fields.type')
            ,dataIndex: 'type'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.fields.values')
            ,dataIndex: 'values'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.fields.format')
            ,dataIndex: 'format'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.fields.required')
            ,dataIndex: 'required'
            ,sortable: true
            ,width: 5
            ,renderer: function(value, p, rec) {
                if(value == 1) {
                    return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" alt="' + _('campaigner.newsletter.approved') + '" />';
                }
                return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" alt="' + _('campaigner.newsletter.unapproved') + '" />';
            }
        },{
            header: _('campaigner.fields.active')
            ,dataIndex: 'active'
            ,sortable: true
            ,width: 5
            ,renderer: function(value, p, rec) {
                if(value == 1) {
                    return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" alt="' + _('campaigner.newsletter.approved') + '" />';
                }
                return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" alt="' + _('campaigner.newsletter.unapproved') + '" />';
            }
        }]
         /* Top toolbar */  
        ,tbar : [{
    	    xtype: 'button'
                ,text: _('campaigner.fields.add')
                ,handler: { xtype: 'campaigner-window-fields-add' ,blankValues: true }
                ,disabled: !MODx.perm.field_create
        }]
        	 // }, '->', {
          //           xtype: 'combo'
          //           ,name: 'public'
          //           ,id: 'campaigner-filter-public'
        	 //    ,store: [
        		// ['-', _('campaigner.all')],
          //               [1, _('campaigner.socialsharing.public')],
          //               [0, _('campaigner.socialsharing.private')]
          //           ]
          //           ,editable: false
          //           ,triggerAction: 'all'
          //           ,lastQuery: ''
          //           ,hiddenName: 'public'
          //           ,submitValue: false
        	 //    ,emptyText: _('campaigner.socialsharing.filter.public')
          //           ,listeners: {
          //               'change': {fn: this.filterPublic, scope: this}
          //               ,'render': {fn: function(cmp) {
          //                   new Ext.KeyMap(cmp.getEl(), {
          //                       key: Ext.EventObject.ENTER
          //                       ,fn: function() {
          //                           this.fireEvent('change',this.getValue());
          //                           this.blur();
          //                           return true;}
          //                       ,scope: cmp
          //                   });
          //               },scope:this}
          //           }
    });
    Campaigner.grid.Fields.superclass.constructor.call(this,config);
};

Ext.extend(Campaigner.grid.Fields,MODx.grid.Grid, {
    getMenu: function() {
        return [{
            text: _('campaigner.fields.update')
            ,handler: this.updateFields
            ,disabled: !MODx.perm.field_edit
        },'-',{
            text: _('campaigner.fields.remove')
            ,handler: this.removeFields
            ,disabled: !MODx.perm.field_remove
        }];
    }
    ,updateFields: function(btn,e) {
        if (!this.updateFieldsWindow) {
            this.updateFieldsWindow = MODx.load({
                xtype: 'campaigner-window-fields-update'
                ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        }
        this.updateFieldsWindow.setValues(this.menu.record);
        this.updateFieldsWindow.show(e.target);
    }
    ,removeFields: function() {
        MODx.msg.confirm({
            title: _('campaigner.fields.remove')
            ,text: _('campaigner.fields.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/fields/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('campaigner-grid-fields',Campaigner.grid.Fields);

Campaigner.window.CreateFields = function(config) {
    Ext.QuickTips.init();
    config = config || {};
    Ext.applyIf(config,{
        title: _('campaigner.fields.add')
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/fields/create'
        }
        ,fileUpload: true
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.fields.name')
            ,name: 'name'
            ,anchor: '100%'
            ,allowBlank: false
            ,blankText: _('campaigner.field.required')
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.fields.label')
            ,name: 'label'
            ,anchor: '100%'
            ,allowBlank: false
            ,blankText: _('campaigner.field.required')
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.fields.type')
            ,name: 'type'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.fields.values')
            ,name: 'values'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.fields.format')
            ,name: 'format'
            ,anchor: '100%'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('campaigner.fields.required')
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.fields.required')
            ,name: 'required'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('campaigner.fields.active')
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.fields.active')
            ,name: 'active'
        }]
    });
    Campaigner.window.CreateFields.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.CreateFields,MODx.Window);
Ext.reg('campaigner-window-fields-add',Campaigner.window.CreateFields);

Campaigner.window.UpdateFields = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('campaigner.fields.update')
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/fields/update'
        }
        ,fileUpload: true
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.fields.name')
            ,name: 'name'
            ,anchor: '100%'
            ,allowBlank: false
            ,blankText: _('campaigner.field.required')
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.fields.label')
            ,name: 'label'
            ,anchor: '100%'
            ,allowBlank: false
            ,blankText: _('campaigner.field.required')
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.fields.type')
            ,name: 'type'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.fields.values')
            ,name: 'values'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.fields.format')
            ,name: 'format'
            ,anchor: '100%'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('campaigner.fields.required')
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.fields.required')
            ,name: 'required'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('campaigner.fields.active')
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.fields.active')
            ,name: 'active'
        }]
    });
    Campaigner.window.UpdateFields.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.UpdateFields,MODx.Window);
Ext.reg('campaigner-window-fields-update',Campaigner.window.UpdateFields);
