Campaigner.grid.SocialSharing = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/socialsharing/getList' }
        ,fields: ['id', 'name', 'url', 'icon', 'active', 'linktext', 'pattern']
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
            header: _('campaigner.socialsharing.name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.socialsharing.url')
            ,dataIndex: 'url'
            ,sortable: true
            ,width: 40
        },{
            header: _('campaigner.socialsharing.linktext')
            ,dataIndex: 'linktext'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.socialsharing.icon')
            ,dataIndex: 'icon'
            ,sortable: true
            ,width: 5
            ,renderer: function(value, p, rec) {return '<img src="' + MODx.config.base_url + '/' + value + '" />'}
        },{
            header: _('campaigner.socialsharing.pattern')
            ,dataIndex: 'pattern'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.socialsharing.active')
            ,dataIndex: 'active'
            ,sortable: true
            ,width: 5
            ,renderer: function(value, p, rec) {
                if(value == 1) {
                    return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" alt="' + _('campaigner.newsletter.approved') + '" />';
                }
                return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" alt="' + _('campaigner.newsletter.unapproved') + '" />';
            }
            // ,renderer: this._renderPublic
        }]
         /* Top toolbar */  
        ,tbar : [{
    	    xtype: 'button'
                ,text: _('campaigner.socialsharing.add')
                ,handler: { xtype: 'campaigner-window-socialsharing-add' ,blankValues: true }
                ,hidden: !MODx.perm.sharing_create
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
    Campaigner.grid.SocialSharing.superclass.constructor.call(this,config);
};

Ext.extend(Campaigner.grid.SocialSharing,MODx.grid.Grid, {
    getMenu: function() {
        var m = [];
        if(MODx.perm.sharing_edit) {
            m.push({
                text: _('campaigner.socialsharing.update')
                ,handler: this.updateSocialSharing
                ,hidden: !MODx.perm.sharing_edit
            });
            m.push('-');
        }

        if(MODx.perm.sharing_remove) {
            m.push({
                text: _('campaigner.socialsharing.remove')
                ,handler: this.removeSocialSharing
                ,hidden: !MODx.perm.sharing_remove
            });
        }

        if (m.length > 0)
                this.addContextMenuItem(m);

        // return [{
        //     text: _('campaigner.socialsharing.update')
        //     ,handler: this.updateSocialSharing
        //     ,hidden: !MODx.perm.sharing_edit
        // },'-',{
        //     text: _('campaigner.socialsharing.remove')
        //     ,handler: this.removeSocialSharing
        //     ,hidden: !MODx.perm.sharing_remove
        // }];
    }
    ,updateSocialSharing: function(btn,e) {
        if (!this.updateSocialSharingWindow) {
            this.updateSocialSharingWindow = MODx.load({
                xtype: 'campaigner-window-socialsharing-update'
                ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        }
        this.updateSocialSharingWindow.setValues(this.menu.record);
        this.updateSocialSharingWindow.show(e.target);
    }
    ,removeSocialSharing: function() {
        MODx.msg.confirm({
            title: _('campaigner.socialsharing.remove')
            ,text: _('campaigner.socialsharing.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/socialsharing/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('campaigner-grid-socialsharing',Campaigner.grid.SocialSharing);

Campaigner.window.CreateSocialSharing = function(config) {
    Ext.QuickTips.init();
    config = config || {};
    Ext.applyIf(config,{
        title: _('campaigner.socialsharing.add')
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/socialsharing/create'
        }
        ,fileUpload: true
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.socialsharing.name')
            ,name: 'name'
            ,anchor: '100%'
            ,allowBlank: false
            ,blankText: _('campaigner.field.required')
        },{
            xtype: 'textarea'
            ,fieldLabel: _('campaigner.socialsharing.url')
            ,name: 'url'
            ,anchor: '100%'
            ,allowBlank: false
            ,blankText: _('campaigner.field.required')
        },{
            xtype: 'modx-combo-browser'
            ,fieldLabel: _('campaigner.socialsharing.icon')
            ,name: 'icon'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.socialsharing.linktext')
            ,name: 'linktext'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.socialsharing.pattern')
            ,name: 'pattern'
            ,anchor: '100%'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('campaigner.socialsharing.active')
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.socialsharing.active')
            ,name: 'active'
        }]
    });
    Campaigner.window.CreateSocialSharing.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.CreateSocialSharing,MODx.Window);
Ext.reg('campaigner-window-socialsharing-add',Campaigner.window.CreateSocialSharing);

Campaigner.window.UpdateSocialSharing = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('campaigner.socialsharing.update')
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/socialsharing/update'
        }
        ,fileUpload: true
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.socialsharing.name')
            ,name: 'name'
            ,anchor: '100%'
            ,allowBlank: false
            ,blankText: _('campaigner.field.required')
        },{
            xtype: 'textarea'
            ,fieldLabel: _('campaigner.socialsharing.url')
            ,name: 'url'
            ,anchor: '100%'
            ,allowBlank: false
            ,blankText: _('campaigner.field.required')
        },{
            xtype: 'modx-combo-browser'
            ,fieldLabel: _('campaigner.socialsharing.icon')
            ,name: 'icon'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.socialsharing.linktext')
            ,name: 'linktext'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.socialsharing.pattern')
            ,name: 'pattern'
            ,anchor: '100%'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('campaigner.socialsharing.active')
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.socialsharing.active')
            ,name: 'active'
        }]
    });
    Campaigner.window.UpdateSocialSharing.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.UpdateSocialSharing,MODx.Window);
Ext.reg('campaigner-window-socialsharing-update',Campaigner.window.UpdateSocialSharing);