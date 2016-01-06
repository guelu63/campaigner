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
            xtype: 'splitbutton'
            ,text: _('campaigner.group.add')
            ,hidden: !MODx.perm.group_create
            ,handler: this.addGroup.createDelegate(this, [0], true)
            ,menu : {
                items: [{
                    text: _('campaigner.group.add_segment')
                    ,handler: this.addGroup.createDelegate(this, [1], true)
                    ,scope : this
                    ,hidden: !MODx.perm.queue_remove_batch
                }]
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
    ,addGroup: function(btn, e, segment) {
      var type;

      type = 'campaigner-window-' + (segment ? 'segment' : 'group');

        // if(!w) {
        	MODx.load({
                xtype: type
                // ,blankValues: true
                // ,listeners: {
                //     'success': {fn:this.refresh,scope:this}
                // }
            }).show();
        // } else {
            // w.reset();
        // }
        // w.show(e.target);
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
            if(MODx.perm.group_edit) {
                m.push({
                    text: _('campaigner.group.edit')
                    ,handler: this.editGroup
                });
            }
            if(MODx.perm.group_remove) {
                m.push({
                    text: _('campaigner.group.remove')
                    ,handler: this.removeGroup
                });
            }
            if(MODx.perm.group_assignment) {
                m.push({
                    text: _('campaigner.group.assign_subscriber')
                    ,handler: this.assignSubscriber
                });
            }
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
        title: _('campaigner.group') + ' ' + (config.record ? config.record.name : '')
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
            ,anchor: '100%'
        }
        // ,{
        //     xtype: 'colorfield'
        //     ,fieldLabel: _('campaigner.group.color')
        //     ,name: 'color'
        //     ,id: 'campaigner-'+this.ident+'-color'
        //     ,showHexValue:true
        //     ,hiddenName: 'color'
        // }
        ,{
            xtype: 'combo'
            ,fieldLabel: _('campaigner.group.priority')
            ,name: 'campaigner'
            ,id: 'campaigner-'+this.ident+'-priority'
            ,anchor: '100%'
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
        },{
            xtype: 'checkbox'
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.group.public')
            ,fieldLabel: _('campaigner.group.public')
            ,name: 'public'
            ,value: 1
            ,id: 'campaigner-'+this.ident+'-public'
        }]
    });
    Campaigner.window.Group.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.Group,MODx.Window);
Ext.reg('campaigner-window-group',Campaigner.window.Group);


Campaigner.window.Segment = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    Ext.applyIf(config,{
        title: _('campaigner.segment') + ' ' + (config.record ? config.record.name : '')
        ,id: 'campaigner-segment-window'
        ,height: 600
        ,width: 750
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/group/segment/save'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: 'campaigner-'+this.ident +'-id'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.group.name')
            ,name: 'name'
            ,anchor: '100%'
            ,allowBlank: false
            ,id: 'campaigner-'+this.ident+'-name'
        },{
            xtype: 'container'
            ,layout: 'column'
            ,items: [{
                xtype: 'checkbox'
                ,hideLabel: true
                ,boxLabel: _('campaigner.group.public')
                ,fieldLabel: _('campaigner.group.public')
                ,name: 'public'
                ,value: 1
                ,id: 'campaigner-'+this.ident+'-public'
                ,columnWidth: .33
            }
            // ,{
            //     xtype: 'colorfield'
            //     ,fieldLabel: _('campaigner.group.color')
            //     ,name: 'color'
            //     ,id: 'campaigner-'+this.ident+'-color'
            //     ,showHexValue:true
            //     ,hiddenName: 'color'
            //     ,columnWidth: .33
            //     ,allowBlank: false
            //     ,wheelImage: this.wheelImage
            //     ,gradientImage: this.gradientImage,
            // }
            ,{
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
                ,columnWidth: .33
            }]
        },{
            xtype: 'fieldset'
            ,id: 'campaigner-segement-filtersets'
            ,checkboxToggle:true
            ,title: _('campaigner.segment.filters')
            ,collapsed: false
            ,items: [{
                xtype: 'campaigner-form-filterfields'
            }]
        },{
            xtype: 'button'
            ,text: _('campaigner.segment.add_filter')
            ,handler: this.addFilterSet
        },{
            xtype: 'container'
            ,height: 200
            ,autoScroll: true
            ,id: 'campaigner-segment-filter-result'
        },{
            xtype: 'hidden'
            ,name: 'subscribers'
            ,id: 'campaigner-segment-subscribers'
        }]
    });
    Campaigner.window.Segment.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.Segment,MODx.Window, {
    addFilterSet: function(b, e) {
        if(!cnt)
            var cnt = 1;
        var sets = Ext.getCmp('campaigner-segement-filtersets');
        sets.add({
            xtype: 'campaigner-form-filterfields'
            ,params: {
                cnt: cnt + 1
            }
        });
        sets.doLayout();
    }
});
Ext.reg('campaigner-window-segment',Campaigner.window.Segment);


Campaigner.panel.FilterFields = function(config) {
    config = config || {};
    var cnt = 1;
    if(config.params)
      cnt = config.params.cnt;
    // console.log(config);
    Ext.applyIf(config,{
        layout: 'column'
        ,id: 'campaigner-segment-filter-fields-' + cnt
        ,style: {
            paddingBottom: '10px'
        }
        ,items: [{
            xtype: 'campaigner-combo-filterkeys'
            ,name: 'key'
            ,columnWidth: .2
            ,listeners: {
                'select': {fn: this.triggerFilter, scope: this}
                // ,'change': {fn this.triggerFilter, scope: this}
            }
        },{
            xtype: 'campaigner-combo-filteroperators'
            ,name: 'operator'
            ,columnWidth: .2
            ,listeners: {
                'select': {fn: this.triggerFilter, scope: this}
                // ,'change': {fn this.triggerFilter, scope: this}
            }
        },{
            xtype: 'textfield'
            ,columnWidth: .2
            ,id: 'campaigner-combo-filtervalue'
            ,name: 'value'
            ,enableKeyEvents: true
            ,listeners: {
                'keyup': {fn: this.triggerFilter, scope: this}
                // ,'change': {fn this.triggerFilter, scope: this}
            }
        },{
            xtype: 'campaigner-combo-filterconditions'
            ,name: 'condition'
            ,columnWidth: .2
            ,listeners: {
                'select': {fn: this.triggerFilter, scope: this}
                // ,'change': {fn this.triggerFilter, scope: this}
            }
        },{
            xtype: 'button'
            ,columnWidth: .1
            ,text: '&times;'
            ,listeners: {
                'click': {fn: this.removeFilter, scope: this}
            }
        }]

    });
    Campaigner.panel.FilterFields.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.panel.FilterFields, Ext.Container, {
    removeFilter: function(btn, e) {
        btn.findParentByType('campaigner-form-filterfields').destroy();
        this.triggerFilter();
    }
    ,triggerFilter: function() {
        var win = Ext.getCmp('campaigner-segment-window');
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/group/filterresult'
                ,data: Ext.util.JSON.encode(win.fp.getForm().getValues())
            }
            ,listeners: {
                'success':{fn:function(response) {
                    var subs_checks = [];
                    Ext.each(response.object.data, function(subscriber, index) {
                        subs_checks.push({
                            boxLabel: subscriber.firstname + ' ' + subscriber.lastname
                             // + '<br/>' + subscriber.email
                            ,name: 'subscriber[]'
                            ,style: {
                                border: '1px solid #cccccc'
                            }
                            ,checked: true
                        });
                    });
                    console.log(response.object.data);
                    var subs_ids = [];
                    // Ext.each(response.object.data, function(subscriber, index) {
                    //     subs_ids.push(subscriber.id);
                    // });

                    // var subs_field = Ext.getCmp('campaigner-segment-subscribers');
                    // subs_field.setValue(subs_ids);

                    var result_container = Ext.getCmp('campaigner-segment-filter-result')
                    result_container.removeAll();
                    result_container.add({
                        xtype: 'checkboxgroup'
                        ,columns: 3
                        ,items: subs_checks
                    });
                    // var store = new Ext.data.ArrayStore({
                    //     fields: ['id', 'firstname', 'lastname', 'email', 'since']
                    //     // ,reader: new Ext.data.JsonReader({
                    //     //     root: 'dataIndexta'
                    //     // })
                    // });
                    // console.log(response.object);
                    // store.loadData(response.object.data);
                    // // console.log(store);


                    // var res_el = result_container.getEl();

                    // // res_el.dom.style.backgroundColor = '#99ff99';
                    // res_el.dom.style.padding = '10px';
                    // res_el.dom.style.marginTop = '10px';

                    // result_container.removeAll();
                    // result_container.add({
                    //     xtype: 'campaigner-segment-grid-subscribers'
                    //     ,id: 'campaigner-segment-grid-subscribers'
                    //     ,store: store
                    //     // ,mode: 'local'
                    //     // ,store: store
                    //     // ,preventRender: true
                    //     // ,listeners: {
                    //     //     'afterrender': {fn: function(g) {
                    //     //         g.refresh();
                    //     //     }, scope: this}
                    //     // }
                    // });

                    result_container.doLayout();
                    // Ext.getCmp('campaigner-segment-grid-subscribers').getStore().loadData(response.object.data);
                    // var grid = Ext.getCmp('campaigner-segment-grid-subscribers');
                    // console.log(grid.getView());
                    // grid.getView().refresh();
                }, scope:this}
            }
        });
        // this.getBottomToolbar().changePage(1);
        // this.refresh();
        // return true;
    }
});
Ext.reg('campaigner-form-filterfields', Campaigner.panel.FilterFields);

Campaigner.grid.SegmentSubscribers = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        fields: ['id', 'firstname', 'lastname', 'email', 'since']
        // ,paging: true
        // ,preventRender: true
        // ,autosave: false
        // ,remoteSort: true
        ,primaryKey: 'id'
        ,columns: [{
            header: _('campaigner.subscriber.email')
            ,dataIndex: 'email'
            ,sortable: true
            ,width: 40
        },{
            header: _('campaigner.subscriber.name')
            ,dataIndex: 'lastname'
            ,sortable: true
            ,width: 20
            // ,renderer: this._renderName
        }]
    });
    Campaigner.grid.SegmentSubscribers.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.grid.SegmentSubscribers,MODx.grid.LocalGrid, {
    _renderName: function(value, p, rec) {
        // console.log(rec);
        return rec.data.firstname + ' ' + rec.data.lastname;
    }
});
Ext.reg('campaigner-segment-grid-subscribers', Campaigner.grid.SegmentSubscribers);


Campaigner.combo.FilterKeys = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: {
            action: 'mgr/group/filterkeys'
        }
        ,fields: ['key', 'value']
        ,displayField: 'key'
        ,valueField: 'value'
        ,emptyText: 'Feld'
        // ,listeners: {
        //     'select': {fn: this.triggerFilter, scope: this}
        // }
    });
    Campaigner.combo.FilterKeys.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.combo.FilterKeys,MODx.combo.ComboBox, {
});
Ext.reg('campaigner-combo-filterkeys', Campaigner.combo.FilterKeys);


/**
    <optgroup label="Numeric">
        <option value="=">=</option>
        <option value="!=">!=</option>
        <option value="&gt;">&gt;</option>
        <option value="&lt;">&lt;</option>
        <option value="&gt;=">&gt;=</option>
        <option value="&lt;=">&lt;=</option>
    </optgroup>
    <optgroup label="String">
        <option value="BEGINS">Begins with</option>
        <option value="END">Ends with</option>
        <option value="CONTAINS">Contains</option>
        <option value="NOTCONTAINS">Does not contain</option>
        <option value="LIKE">LIKE</option>
        <option value="NOT LIKE">NOT LIKE</option>
        <option value="REGEXP">REGEXP</option>
        <option value="NOT REGEXP">NOT REGEXP</option>
    </optgroup>
    <optgroup label="Other">
        <option value="IS NULL">IS NULL</option>
        <option value="IS NOT NULL">IS NOT NULL</option>
    </optgroup>
</select>
*/

Campaigner.combo.FilterOperators = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        mode: 'local'
        ,fields: ['key', 'value']
        ,displayField: 'value'
        ,valueField: 'key'
        ,emptyText: 'Operator'
        ,store: [
            ['=', '=']
            ,['!=', '!=']
            ,['>', '>']
            ,['<', '<']
            ,['>=', '>=']
            ,['<=', '<=']
            ,['LIKE', 'LIKE']
            ,['NOT LIKE', 'NOT LIKE']
            ,['IS NULL', 'IS NULL']
            ,['IS NOT NULL', 'IS NOT NULL']
        ]
    });
    Campaigner.combo.FilterOperators.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.combo.FilterOperators,MODx.combo.ComboBox);
Ext.reg('campaigner-combo-filteroperators', Campaigner.combo.FilterOperators);

Campaigner.combo.FilterConditions = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        mode: 'local'
        ,fields: ['key', 'value']
        ,displayField: 'value'
        ,valueField: 'key'
        ,emptyText: 'Condition'
        ,store: [
            ['AND', 'AND']
            ,['OR', 'OR']
        ]
    });
    Campaigner.combo.FilterConditions.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.combo.FilterConditions,MODx.combo.ComboBox);
Ext.reg('campaigner-combo-filterconditions', Campaigner.combo.FilterConditions);



Campaigner.window.AssignSubscriber = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    var record = config.record;
    Ext.applyIf(config,{
        title: _('campaigner.group') + ' ' + record.name + ': ' + _('campaigner.group.assign_subscriber')
        // ,id: this.ident
        ,height: 500
        ,width: 750
        ,autoScroll: true
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
        ,success: function(t) {
            t.suspendEvents();
        }
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

/**
 * @todo  Keep/store grid selections when multi-paging
 */

Campaigner.grid.GroupSubscribers = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    var tt = new Ext.ux.grid.CheckColumn({
        header: _('campaigner.group.subscriber_assigned')
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
            console.log(ids.join(","));
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
        ,fields: ['id', 'email', 'firstname', 'lastname', 'assigned', 'active']
        ,paging: true
        ,pageSize: 10
        ,plugins: tt
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        // ,sm: this.sm
        ,columns: [
            tt
            // this.sm,
        // ,{
        //     header: _('campaigner.subscriber.id')
        //     ,dataIndex: 'id'
        //     ,sortable: true
        //     ,width: 10
        // }
        ,{
            header: _('campaigner.subscriber.active')
            ,dataIndex: 'active'
            ,sortable: true
            ,width: 8
            ,renderer: this._renderPublic
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
        , tbar: [{
            xtype: 'textfield'
            ,name: 'search'
            ,id: 'campaigner-filter-search-group-subscribers'
            ,emptyText: _('campaigner.subscriber.search')+'...'
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
        }]
    });
    Campaigner.grid.GroupSubscribers.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.grid.GroupSubscribers,MODx.grid.Grid
    ,{
        constructor: function(cfg) {
            this.initConfig(cfg);
        }
        ,filterSearch: function(tf,newValue,oldValue) {
            var nv = newValue;
            this.getStore().baseParams.search = nv;
            this.getBottomToolbar().changePage(1);
            this.refresh();
            return true;
        }
        ,_renderPublic: function(value, p, rec) {
            if(value == 1)
                return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" class="small" alt="' + _('campaigner.group.public') + '" />';
            return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" class="small" alt="' + _('campaigner.group.private') + '" />';
        }
    }
);
Ext.reg('campaigner-grid-group-subscribers',Campaigner.grid.GroupSubscribers);
