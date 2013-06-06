Campaigner.grid.Group = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/group/getList' }
        ,fields: ['id', 'name', 'subscribers', 'total', 'active', 'public', 'color', 'priority']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        ,columns: [{
            header: _('campaigner.group.name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 40
        },{
            header: _('campaigner.group.subscribers')
            ,dataIndex: 'total'
            ,sortable: true
            ,width: 20
        },{
            header: _('campaigner.group.active')
            ,dataIndex: 'active'
            ,sortable: true
            ,width: 20
            ,renderer: this._renderActive
        },{
            header: _('campaigner.group.public')
            ,dataIndex: 'public'
            ,sortable: true
            ,width: 20
            ,renderer: this._renderPublic
        },{
            header: _('campaigner.group.color')
            ,dataIndex: 'color'
            ,sortable: true
            ,width: 20
            ,renderer: this._renderColor
        },{
            header: _('campaigner.group.priority')
            ,dataIndex: 'priority'
            ,sortable: true
            ,width: 20
        }],  
        /* Top toolbar */  
        tbar : [{
            xtype: 'combo'
            ,name: 'public'
            ,id: 'campaigner-filter-public'
            ,store: [
            ['-', _('campaigner.all')],
            [1, _('campaigner.group.public')],
            [0, _('campaigner.group.private')]
            ]
            ,editable: false
            ,triggerAction: 'all'
            ,lastQuery: ''
            ,hiddenName: 'public'
            ,submitValue: false
            ,emptyText: _('campaigner.group.filter.public')
            ,listeners: {
                'change': {fn: this.filterPublic, scope: this}
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
        }, '->', {
            xtype: 'button'
            ,id: 'campaigner-group-add'
            ,text: _('campaigner.group.add')
            ,listeners: {
                'click': {fn: this.addGroup, scope: this}
            }
        }]
    });
Campaigner.grid.Group.superclass.constructor.call(this,config)
};

Ext.extend(Campaigner.grid.Group,MODx.grid.Grid,{
    _renderColor: function(value, p, rec) {
        return '<div class="group-small" style="background: '+ value +';"></div><span style="color: '+ value +'">' + value + '</span>';
    }
    ,_renderPublic: function(value, p, rec) {
        if(value == 1)
            return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" class="small" alt="' + _('campaigner.group.public') + '" />';
        return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" class="small" alt="' + _('campaigner.group.private') + '" />';
    }
    ,_renderActive: function(value, p, rec) {
        if(rec.data.members < 1) return value;
        return value + ' ( '+ Math.round(value*100/rec.data.total) +'%) ';
    }
    ,filterPublic: function(tf,newValue,oldValue) {
        var nv = newValue;
        var s = this.getStore();
        if(nv == '-') {
            delete s.baseParams.public;
        } else {
            s.baseParams.public = nv;
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
    ,addGroup: function(e) {
    	var w = MODx.load({
            xtype: 'campaigner-window-group'
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        w.show(e.target);
    }
    ,editGroup: function(e) {
        var w = MODx.load({
            xtype: 'campaigner-window-group'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        w.setValues(this.menu.record);
        w.show(e.target);
    }
    ,removeGroup: function(e) {
        MODx.msg.confirm({
            title: _('campaigner.group.remove.title')
            ,text: _('campaigner.group.remove.confirm')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/group/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,assignSubscriber: function(e) {
        var w = MODx.load({
            xtype: 'campaigner-window-assign-subscriber'
            ,record: this.menu.record
            ,group_id: this.menu.record
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        w.setValues(this.menu.record);
        w.show(e.target);

        var grid_subs = Ext.getCmp('campaigner-grid-group-subscribers');
        grid_subs.store.load({params:{group_id: this.menu.record.id || 0}});
    }
    ,getMenu: function() {
        var m = [];
        if (this.getSelectionModel().getCount() == 1) {
            var rs = this.getSelectionModel().getSelections();
            m.push({
                text: _('campaigner.group.edit')
                ,handler: this.editGroup
            });
            m.push({
                text: _('campaigner.group.remove')
                ,handler: this.removeGroup
            });
            m.push({
                text: _('campaigner.group.assign_subscriber')
                ,handler: this.assignSubscriber
            })
        }
        if (m.length > 0) {
            this.addContextMenuItem(m);
        }
    }
    // ,viewOptions: function(btn,e) {
    //     if (!this.updateDoodleListWindow) {
    //         this.updateDoodleListWindow = MODx.load({
    //             xtype: 'campaigner-window-assign-subscriber'
    //             ,record: this.menu.record
    //             ,group_id: this.menu.record.id // pass the doodle id on to the window so it can be passed to the grid for the first time it opens
    //             ,listeners: {
    //                 'success': {fn:this.refresh,scope:this}
    //             }
    //         });
    //     } 
    //     this.updateDoodleListWindow.setValues(this.menu.record);
    //     this.updateDoodleListWindow.show(e.target);
    //     var optionsgrid = Ext.getCmp('campaigner-grid-group-subscribers');
    //     // must define action here, otherwise the baseParams will remove it and cause the grid to never load
    //     optionsgrid.store.baseParams = {action: 'mgr/group/getsubscriberlist',group_id: this.menu.record.id || 0,start: 0 ,limit: 20};
    //     // still have to load the grid
    //     optionsgrid.store.load();

    //     // optionsgrid.store.load({params:{doodle_id: this.menu.record.id || 0}}); removed this line
    // }
});
Ext.reg('campaigner-grid-group',Campaigner.grid.Group);

Campaigner.window.Group = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    Ext.applyIf(config,{
        title: _('campaigner.group')
        ,id: this.ident
        ,height: 400
        ,width: 300
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/group/save'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: 'campaigner-'+this.ident +'-id'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.group.name')
            ,name: 'name'
            ,id: 'campaigner-'+this.ident+'-name'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('campaigner.group.public')
            ,name: 'public'
            ,value: 1
            ,id: 'campaigner-'+this.ident+'-public'
        },{
            xtype: 'colorfield'
            ,fieldLabel: _('campaigner.group.color')
            ,name: 'color'
            ,id: 'campaigner-'+this.ident+'-color'
            ,showHexValue:true
            ,hiddenName: 'color'
        },{
            xtype: 'combo'
            ,fieldLabel: _('campaigner.group.priority')
            ,name: 'campaigner'
            ,id: 'campaigner-'+this.ident+'-priority'
            ,width: 200
            ,emptyText: '5'
            ,fields: [
            'id',
            'display'
            ]
            ,store: [
            [1, 'Sehr wichtig'],
            [2, 'Schon wichtig'],
            [3, 'Wichtig'],
            [4, 'Geht so'],
            [5, 'Unwichtig']
            ]
            ,valueField: 'id'
            ,displayField: 'display'
            ,triggerAction: 'all'
            ,forceSelection: true
            ,lastQuery: ''
            ,triggerAction: 'all'
            ,hiddenName: 'priority'
        }]
    });
Campaigner.window.Group.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.Group,MODx.Window);
Ext.reg('campaigner-window-group',Campaigner.window.Group);
    
Campaigner.window.AssignSubscriber = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    var record = config.record;
    Ext.applyIf(config,{
        title: _('campaigner.group.assign_subscriber')
        // ,id: this.ident
        ,height: 500
        ,width: 750
        ,id: this.ident
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/group/assignsubscriber'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: 'campaigner-'+this.ident +'-id'
        },{
            xtype: 'hidden'
            ,id: 'campaigner-group-subscriber-assignments'
            ,name: 'assigned'
        }]
        ,items: [
        {
            html: '<p>'+_('campaigner.group.assign_subscriber_info')+'</p>',
            border: false,
            bodyStyle: 'padding: 10px'
        },{
            xtype: 'campaigner-grid-group-subscribers'
            // ,fieldLabel: _('campaigner.statistics_details')
            ,id: 'campaigner-grid-group-subscribers'
            ,scope: this
            ,preventRender: true
            ,style: 'padding: 10px'
        }]
    });
    Campaigner.window.AssignSubscriber.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.AssignSubscriber,MODx.Window
    ,{
        constructor: function(cfg) {
            this.initConfig(cfg);
        }
    }
);
Ext.reg('campaigner-window-assign-subscriber',Campaigner.window.AssignSubscriber);

Campaigner.grid.GroupSubscribers = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    var tt = new Ext.ux.grid.CheckColumn({
        header: 'test'
        ,dataIndex: 'assigned'
        ,width: 10
        ,sortable: false
        ,onMouseDown: function(e, t){
            if(t.className && t.className.indexOf('x-grid3-cc-'+this.id) != -1){
                e.stopEvent();
                var index = this.grid.getView().findRowIndex(t);
                var record = this.grid.store.getAt(index);
                record.set(this.dataIndex, !record.data[this.dataIndex]);
            }
            // Find records with indoor=true
            var grid = Ext.getCmp('campaigner-grid-group-subscribers');
            var records = grid.getStore().queryBy(function(record) {
                return record.get('assigned') === true;
            });
            // Collect ids of those records
            var ids = [];
            records.each(function(record) {
                ids.push(record.get('id'));
            });
            var hidden = Ext.getCmp('campaigner-group-subscriber-assignments');
            hidden.setValue(ids.join(","));
            return;
        }
    });
    // this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        id: 'campaigner-grid-group-subscribers'
        ,url: Campaigner.config.connector_url
        ,baseParams: {
            action: 'mgr/group/getsubscriberlist'
            ,group_id: config.scope.record.id
        }
        ,fields: ['id', 'email', 'firstname', 'lastname', 'assigned']
        ,paging: true
        ,pageSize: 10
        ,plugins: tt
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        // ,sm: this.sm
        ,columns: [
            tt,
            // this.sm,
        {
            header: _('campaigner.subscriber.id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 10
        }
        ,{
            header: _('campaigner.subscriber.email')
            ,dataIndex: 'email'
            ,sortable: true
            ,width: 40
        }
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
        }]
    });
    Campaigner.grid.GroupSubscribers.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.grid.GroupSubscribers,MODx.grid.Grid
    ,{
        constructor: function(cfg) {
            this.initConfig(cfg);
        }
    }
);
Ext.reg('campaigner-grid-group-subscribers',Campaigner.grid.GroupSubscribers);