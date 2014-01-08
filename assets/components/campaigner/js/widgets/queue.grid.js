Campaigner.grid.Queue = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/queue/getList', showProcessed: 0 }
        ,fields: ['id', 'subscriber', 'newsletter', 'state', 'subject', 'date', 'firstname', 'lastname', 'text', 'email', 'sent', 'bounced', 'total', 'priority', 'error', 'created', 'scheduled']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        ,grouping: true
        ,groupBy: 'newsletter'
        ,singleText: _('campaigner.queue.group_single')
        ,pluralText: _('campaigner.queue.group_plural')
        ,sm: this.sm
        ,columns: [
            this.sm,
        {
            header: _('campaigner.queue.newsletter')
            ,dataIndex: 'newsletter'
            ,sortable: true
            ,width: 30
            ,renderer: this._renderNewsletter
        },{
            header: _('campaigner.queue.receiver')
            ,dataIndex: 'subscriber'
            ,sortable: true
            ,width: 40
            ,renderer: this._renderSubscriber
        },{
            header: _('campaigner.queue.state')
            ,dataIndex: 'state'
            ,sortable: true
            ,width: 10
            ,renderer: this._renderState
        }
        // ,{
        //     header: _('campaigner.queue.priority')
        //     ,dataIndex: 'priority'
        //     ,sortable: true
        //     ,width: 10
        // }
        ,{
            header: _('campaigner.queue.sent')
            ,dataIndex: 'sent'
            ,sortable: true
            ,width: 20
        },{
            header: _('campaigner.queue.created')
            ,dataIndex: 'created'
            ,sortable: true
            ,width: 20
        },{
            header: _('campaigner.queue.scheduled')
            ,dataIndex: 'scheduled'
            ,sortable: true
            ,width: 20
        },{
            header: _('campaigner.queue.error')
            ,dataIndex: 'error'
            ,sortable: true
            ,width: 20
        }],
        /* Top toolbar */
        tbar : [{

            xtype: 'splitbutton'
            ,text: _('campaigner.queue.batch_actions')
            ,hidden: !MODx.perm.queue_remove_batch || !MODx.perm.queue_send_batch
            // ,handler: this.cleanerMedia.createDelegate(this, [{cleaner: 1}])
            // ,tooltip: {text:'This is a an example QuickTip for a toolbar item', title:'Tip Title'}
            // Menus can be built/referenced by using nested menu config objects
            ,menu : {
                items: [{
                    text: _('campaigner.queue.remove_marked')
                    ,handler: this.removeQueue
                    ,scope : this
                    ,hidden: !MODx.perm.queue_remove_batch
                }, {
                    text: _('campaigner.queue.send_marked')
                    ,handler: this.processQueue
                    ,scope : this
                    ,hidden: !MODx.perm.queue_send_batch
                },{
                    text: _('campaigner.queue.set_state')
                    ,handler: this.setState
                    ,scope: this
                    ,hidden: !MODx.perm.queue_set_state
                }]
            }
        }, {
            xtype: 'splitbutton'
            ,id: 'campaigner-filter-queue'
            ,text: _('campaigner.queue.filter_processed')
            ,handler: this.filterQueue.createDelegate(this, [1], true)
            // ,listeners: {
            //     'click': {fn: this.filterQueue, scope: this}
            // }
            ,menu: {
                items: [{
                    text: _('campaigner.queue.filter_unprocessed')
                    ,handler: this.filterQueue.createDelegate(this, [0], true)
                    ,scope: this
                    // ,listeners: {
                    //     'click': {fn: this.filterQueue, scope: this}
                    // }
                },{
                    text: _('campaigner.queue.filter_currentbatch')
                    ,handler: this.filterQueue.createDelegate(this, [3], true)
                    ,scope: this
                    // ,listeners: {
                    //     'click': {fn: this.filterQueue, scope: this}
                    // }
                },{
                    text: _('campaigner.queue.filter_halted')
                    ,handler: this.filterQueue.createDelegate(this, [5], true)
                    ,scope: this
                    // ,listeners: {
                    //     'click': {fn: this.filterQueue, scope: this}
                    // }
                },{
                    text: _('campaigner.queue.filter_resend')
                    ,handler: this.filterQueue.createDelegate(this, [8], true)
                    ,scope: this
                    // ,listeners: {
                    //     'click': {fn: this.filterQueue, scope: this}
                    // }
                },{
                    text: _('campaigner.queue.filter_failed')
                    ,handler: this.filterQueue.createDelegate(this, [6], true)
                    ,scope: this
                    // ,listeners: {
                    //     'click': {fn: this.filterQueue, scope: this}
                    // }
                }]
            }
        }, {xtype: 'tbfill'}, {
            xtype: 'splitbutton'
            ,text: _('campaigner.queue.actions')
            ,menu: {
                items: [{
                    id: 'campaigner-process-queue'
                    ,text: _('campaigner.queue.process_queue')
                    ,hidden: !MODx.perm.queue_process
                    ,listeners: {
                        'click': {fn: this.processQueue, scope: this}
                    }
                },{
                    id: 'campaigner-remove-tests'
                    ,text: _('campaigner.queue.remove_tests')
                    ,hidden: !MODx.perm.queue_remove_tests
                    ,listeners: {
                        'click': {fn: this.removeTests, scope: this}
                    }
                },{
                    text: _('campaigner.queue.logwindow')
                    ,listeners: {
                        'click': {fn: this.logWindow, scope: this}
                    }
                }]
            }
        },{
            xtype: 'textfield'
            ,name: 'search'
            ,id: 'campaigner-queue-filter-search'
            ,emptyText: _('search')+'...'
            ,listeners: {
                'change': {fn: this.filterSearch, scope: this}
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
    Campaigner.grid.Queue.superclass.constructor.call(this,config);
};

Ext.extend(Campaigner.grid.Queue,MODx.grid.Grid,{
    _renderNewsletter: function(value, p, rec) {
        return rec.data.subject;
    }
    ,_renderSubscriber: function(value, p, rec) {
        return '<span class="subscriber">' + rec.data.email + '</span> (' + value + ')';
    }
    ,_renderState: function(value, p, rec) {
        if(value == 1)
            return '<img src="'+ Campaigner.config.base_url +'images/sent.png" alt="' + _('campaigner.queue.sent') + '" />';
        return '<img src="'+ Campaigner.config.base_url +'images/waiting.png" alt="' + _('campaigner.queue.waiting') + '" />';
    }
    ,filterQueue: function(btn, e, state) {
        var s = this.getStore();
        // if (btn.text ==  _('campaigner.queue.show_processed')) {
        // s.setBaseParam('showProcessed',state);
        s.setBaseParam('state',state);
        // btn.setText(_('campaigner.queue.hide_processed'))
        // } else {
        //     s.setBaseParam('showProcessed',0);
        // btn.setText(_('campaigner.queue.show_processed'))
        // }
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,toggleProcessed: function(btn, e) {
        var s = this.getStore();
        if (btn.text ==  _('campaigner.queue.show_processed')) {
            s.setBaseParam('showProcessed',1);
            btn.setText(_('campaigner.queue.hide_processed'));
        } else {
            s.setBaseParam('showProcessed',0);
            btn.setText(_('campaigner.queue.show_processed'));
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,removeQueue: function(e) {
        var cs = this.getSelectedAsList();
        if (cs === false) {return false;}
        // var msg;
        // if(this.menu.record.state === 0) {
        //     msg = _('campaigner.queue.remove.unsend');
        // } else {
        //     msg = _('campaigner.queue.remove.confirm');
        // }
        MODx.msg.confirm({
            title: _('campaigner.queue.remove.title')
            ,text: _('campaigner.queue.remove_info')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/queue/remove'
                ,marked: cs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,processQueue: function() {
        var cs = this.getSelectedAsList();
        if (cs === false) {return false;}
        MODx.msg.confirm({
            title: _('campaigner.queue.process_queue')
            ,text: _('campaigner.queue.process_queue_text')
            ,multiline: true
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/queue/process'
                ,marked: cs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,setState: function(btn, e) {
        var cs = this.getSelectedAsList();
        if (cs === false) {return false;}
        if (!this.updateQueueStateWindow) {
            this.updateQueueStateWindow = MODx.load({
                xtype: 'campaigner-window-queuestate'
                ,baseParams: {
                    action: 'mgr/queue/setstate'
                    ,marked: cs
                }
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        }
        // this.updateQueueStateWindow.setValues(this.menu.record);
        this.updateQueueStateWindow.show(e.target);
        // MODx.msg.confirm({
        //     title: _('campaigner.queue.set_state')
        //     ,text: _('campaigner.queue.set_state_text')
        //     ,url: Campaigner.config.connector_url
        //     ,params: {
        //         action: 'mgr/queue/setstate'
        //         ,marked: cs
        //     }
        //     ,listeners: {
        //         'success': {fn:this.refresh,scope:this}
        //     }
        // });
    }
    ,removeTests: function() {
        MODx.msg.confirm({
            title: _('campaigner.queue.remove_tests')
            ,text: _('campaigner.queue.remove_tests_text')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/queue/remove_tests'
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,logWindow: function(btn, e) {
        if (!this.queueLogWindow) {
            this.queueLogWindow = MODx.load({
                xtype: 'campaigner-queue-logwindow'
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                    ,'close': function() {
                        Ext.TaskManager.stop(task);
                    }
                }
            });
        }
        this.queueLogWindow.setValues(this.menu.record);
        this.queueLogWindow.show(e.target);
    }
    ,getMenu: function() {
        var m = [];
        if (this.getSelectionModel().getCount() == 1) {
            var rs = this.getSelectionModel().getSelections();
            if(MODx.perm.queue_remove) {
                m.push({
                    text: _('campaigner.queue.remove')
                    ,handler: this.removeQueue
                });
            }
            if(MODx.perm.queue_send) {
                m.push({
                    text: _('campaigner.queue.send')
                    ,handler: this.processQueue
                });
            }
        }
        if (m.length > 0)
            this.addContextMenuItem(m);
    }
    ,filterSearch: function(tf,newValue,oldValue) {
        var nv = newValue;
        this.getStore().baseParams.search = nv;
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
});
Ext.reg('campaigner-grid-queue',Campaigner.grid.Queue);

Campaigner.window.QueueState = function(config) {
    config = config || {};
    console.log(config);
    Ext.applyIf(config,{
        title: _('campaigner.queue.set_state')
        ,url: Campaigner.config.connector_url
        ,fields: [{
            xtype: 'panel'
            ,html: _('campaigner.queue.set_state_text')
        },{
            xtype: 'campaigner-combo-states'
            ,fieldLabel: _('campaigner.queue.combo_states')
            ,name: 'state'
            ,anchor: '100%'
        }]
    });
    Campaigner.window.QueueState.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.window.QueueState,MODx.Window);
Ext.reg('campaigner-window-queuestate', Campaigner.window.QueueState);

Campaigner.combo.States = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: [0,1]
        ,mode: 'local'
        ,editable: false
    });
    Campaigner.combo.States.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.combo.States,MODx.combo.ComboBox);
Ext.reg('campaigner-combo-states', Campaigner.combo.States);

Campaigner.window.QueueLogWindow = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('campaigner.queue.logwindow.title')
        ,width: 500
        ,fields: [{
            xtype: 'container'
            ,layout: 'column'
            ,items: [{
                xtype: 'campaigner-combo-intervals'
                ,columnWidth: .5
                
            },{
                xtype: 'campaigner-combo-logfiles'
                ,columnWidth: .5
            }]
        },{
            xtype: 'panel'
            ,id: 'campaigner-queue-taillog'
            ,height: 300
            ,border: true
            ,autoScroll: true
            ,listeners: {
                'afterrender': {fn: this.refreshLog, scope: this}
            }
        }]
    });
    Campaigner.window.QueueLogWindow.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.window.QueueLogWindow, MODx.Window, {
    refreshLog: function(el) {
        
        var el = el;
        var stop = false;
        var task = {
            run: function(){
                if(!stop){
                    MODx.Ajax.request({
                        url: Campaigner.config.connector_url
                        ,params: {
                            action: 'mgr/queue/log/tail'
                        }
                        ,listeners: {
                            'success': {fn:function(r) {
                                el.removeAll();
                                Ext.each(r.object, function(line, index) {
                                    el.add({
                                        xtype: 'container'
                                        ,style: 'font-family:Courier New;font-size:11px'
                                        ,html: '<p>' + line + '</p>'
                                    });
                                    
                                })
                                el.doLayout();
                                // stop = true;
                            },scope:this}
                        }
                    });
                    // stop = true;
                }else{
                    runner.stop(task); // we can stop the task here if we need to.
                }
            },
            interval: 10000 // every 30 seconds
        };
        var runner = new Ext.util.TaskRunner();
        runner.start(task);
    }
});
Ext.reg('campaigner-queue-logwindow', Campaigner.window.QueueLogWindow);

Campaigner.combo.Logfile = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'logfile'
        ,emptyText: _('campaigner.queue.logwindow.file_empty')
        ,displayField: 'filename'
        ,valueField: 'filename'
        ,fields: ['filename']
        ,url: Campaigner.config.connector_url
        ,baseParams: {
            action: 'mgr/queue/log/getlist.class'
            ,combo: true
        }
    });
    Campaigner.combo.Logfile.superclass.constructor.call(this, config);
}
Ext.extend(Campaigner.combo.Logfile, MODx.combo.ComboBox);
Ext.reg('campaigner-combo-logfiles', Campaigner.combo.Logfile);

Campaigner.combo.Interval = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: ['5','10','15','20','25','30']// this is the local values
        ,mode: 'local'
        ,editable: false
        ,name: 'interval'
        ,emptyText: _('campaigner.queue.logwindow.interval_empty')
    });
    Campaigner.combo.Interval.superclass.constructor.call(this, config);
}
Ext.extend(Campaigner.combo.Interval, MODx.combo.ComboBox);
Ext.reg('campaigner-combo-intervals', Campaigner.combo.Interval);