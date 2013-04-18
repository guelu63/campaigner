/*
 ######################################################
    GRIDS
 ######################################################
*/
Campaigner.grid.Soft = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/bounce/getSoftList' }
        ,fields: ['id', 'subscriber', 'name', 'email', 'type', 'count', 'last', 'active']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
	,sm: this.sm
        ,columns: [this.sm,{
            header: _('campaigner.subscriber.id')
            ,dataIndex: 'subscriber'
            ,width: 30
        },{
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
            header: _('campaigner.bounce.count')
            ,dataIndex: 'count'
            //,width: 10
        },{
            header: _('campaigner.bounce.last')
            ,dataIndex: 'last'
            //,width: 10
        }],
	tbar : [{
	    xtype: 'button'
            ,text: _('campaigner.bounce.soft.deleteMarkedSubscribers')
            ,handler: this.deleteSubscriber
	 }, {  
            xtype : "button"
            ,text : _( "campaigner.bounce.soft.deactivateMarkedSubscribers" )
	    ,handler: this.deactivateSubscriber            
        }, {  
            xtype : "button"
            ,text : _( "campaigner.bounce.soft.ctivateMarkedSubscribers" )
	    ,handler: this.activateSubscriber            
        }]
    });
    Campaigner.grid.Soft.superclass.constructor.call(this,config)
};
Ext.extend(Campaigner.grid.Soft,MODx.grid.Grid,{
    showDetails: function(e) {
	softDetailWindow = MODx.load({
	    xtype: 'campaigner-window-softDetail'
	    ,record: this.menu.record
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        softDetailWindow.setValues(this.menu.record);
        softDetailWindow.show(e.target);
    }
    ,_renderActive: function(value, p, rec) {
	if(value == 1) {
	    return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" class="small" alt="" />';
	}
	return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" class="small" alt="" />';
    }
    ,deleteSubscriber: function() {
	var cs = this.getSelectedAsList();
        
        if (cs === false) { return false };
        
	MODx.msg.confirm({
            title: _('campaigner.bounce.soft.removeTitle')
            ,text: _('campaigner.bounce.soft.removeConfirm')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/bounce/deleteSubscriber'
		,markedSubscribers: cs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
	return true;
    }
    ,deactivateSubscriber: function() {
	var cs = this.getSelectedAsList();
        
        if (cs === false) { return false };
        
	MODx.msg.confirm({
            title: _('campaigner.bounce.soft.deactivateTitle')
            ,text: _('campaigner.bounce.soft.deactivateConfirm')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/bounce/deactivateSubscriber'
		,markedSubscribers: cs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
	return true;
    }
    ,activateSubscriber: function() {
	var cs = this.getSelectedAsList();
        
        if (cs === false) { return false };
        
	MODx.msg.confirm({
            title: _('campaigner.bounce.soft.activateTitle')
            ,text: _('campaigner.bounce.soft.activateConfirm')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/bounce/activateSubscriber'
		,markedSubscribers: cs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
	return true;
    }
    ,getMenu: function() {
	var m = [];
	if (this.getSelectionModel().getCount() == 1) {
            var rs = this.getSelectionModel().getSelections();
	    m.push({
		text: _('campaigner.bounce.soft.details')
		,handler: this.showDetails
	    });
	    //Ext.Msg.alert("Notification",this.menu.record.active);
	    //wenn der Subscriber aktiv ist
	    if(this.menu.record.active==1) {
		//Dann kann er deaktiviert werden
		m.push('-');
		m.push({
		    text: _('campaigner.bounce.soft.deactivateSubscriber')
		    ,handler: this.deactivateSubscriber
		});
	    }
	    //wenn er jedoch deaktiviert ist
	    else if(this.menu.record.active==0) {
		//Dann kann er aktviert werden
		m.push('-');
		m.push({
		    text: _('campaigner.bounce.soft.activateSubscriber')
		    ,handler: this.activateSubscriber
		});
	    }
	    m.push('-');
	    m.push({
		text: _('campaigner.bounce.soft.deleteSubscriber')
		,handler: this.deleteSubscriber
	    });
	}
	if (m.length > 0) {
            this.addContextMenuItem(m);
        }
    }
});
Ext.reg('campaigner-grid-bounce-soft',Campaigner.grid.Soft);

Campaigner.grid.Hard = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/bounce/getHardList' }
        ,fields: ['id', 'subscriber', 'name', 'email', 'newsletterTitle', 'reason', 'recieved', 'docid', 'state', 'code', 'count', 'last', 'groups']
        ,paging: true
        ,autosave: false
        ,remoteSort: false
	,sm: this.sm
        ,primaryKey: 'id'
        ,columns: [this.sm,{
            header: _('campaigner.subscriber.id')
            ,dataIndex: 'subscriber'
            ,width: 30
        },{
            header: _('campaigner.bounce.name')
            ,dataIndex: 'name'
            //,width: 10
        },{
            header: _('campaigner.subscriber.email')
            ,dataIndex: 'email'
            //,width: 10
        },{
            header: _('campaigner.subscriber.groups')
            ,dataIndex: 'groups'
            //,width: 10
	    ,renderer: this._renderGroups
        },{
            header: _('campaigner.bounce.code')
            ,dataIndex: 'code'
			,sortable: true
            //,width: 10
        },{
            header: _('campaigner.bounce.reason')
            ,dataIndex: 'reason'
			,sortable: true
            //,width: 10
        },{
            header: _('campaigner.bounce.recieved')
            ,dataIndex: 'recieved'
			,sortable: true
            //,width: 10
        }],
	tbar : [{
	    xtype: 'button'
            ,text: _('campaigner.bounce.soft.deleteMarkedSubscribers')
            ,handler: this.deleteSubscriber
	 }]
    });
    Campaigner.grid.Hard.superclass.constructor.call(this,config)
};

Ext.extend(Campaigner.grid.Hard,MODx.grid.Grid,{
    getMenu: function() {
	var m = [];
	m.push({
	    text: _('campaigner.bounce.hard.reactivate')
	    ,handler: this.reactivate
	});
	m.push('-');
	m.push({
	    text: _('campaigner.bounce.soft.deleteSubscriber')
	    ,handler: this.deleteSubscriber
	});
	if (m.length > 0) {
            this.addContextMenuItem(m);
        }
    }
    ,deleteSubscriber: function() {
	var cs = this.getSelectedAsList();
        
        if (cs === false) { return false };
        
	MODx.msg.confirm({
            title: _('campaigner.bounce.soft.removeTitle')
            ,text: _('campaigner.bounce.soft.removeConfirm')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/bounce/deleteSubscriber'
		,markedSubscribers: cs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
	return true;
    }
    ,reactivate: function(e) {
	this.updateWindow = MODx.load({
	    xtype: 'campaigner-window-reactivate'
	    ,record: this.menu.record
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        this.updateWindow.setValues(this.menu.record);
        this.updateWindow.show(e.target);
    }
    ,_renderGroups: function(value, p, rec) {
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
});
Ext.reg('campaigner-grid-bounce-hard',Campaigner.grid.Hard);

Campaigner.grid.Resend = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/bounce/getResendList' }
        ,fields: ['id', 'queue_id', 'name', 'email', 'newsletterTitle', 'sent', 'docid', 'state_msg', 'state']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
	,sm: this.sm
        ,columns: [this.sm,{
            header: _('campaigner.bounce.newsletter')
            ,dataIndex: 'newsletterTitle'
            //,width: 300
	    ,renderer: this._renderNewsletter
        },{
            header: _('campaigner.bounce.recipient')
            ,dataIndex: 'name'
            //,width: 10
        },{
            header: _('campaigner.subscriber.email')
            ,dataIndex: 'email'
            //,width: 10
        },{
            header: _('campaigner.bounce.time')
            ,dataIndex: 'sent'
            //,width: 10
        },{
            header: _('campaigner.bounce.state')
            //,width: 250
	    ,dataIndex: 'state_msg'
        }],
	tbar : [{
	    xtype: 'button'
            ,text: _('campaigner.bounce.resend.deleteMarkedJobs')
            ,handler: this.deleteJob
	 }]
    });
    Campaigner.grid.Resend.superclass.constructor.call(this,config)
};

Ext.extend(Campaigner.grid.Resend,MODx.grid.Grid,{
    _renderNewsletter: function(value, p, rec) {
	return '<a href="?a=30&id='+ rec.data.docid +'">'+ value +'</a>';
    }
    ,getMenu: function() {
	var m = [];
	if (this.getSelectionModel().getCount() == 1) {
            var rs = this.getSelectionModel();
	    
	    if(rs.getSelected().get('state') == "0") {
		m.push({
		    text: _('campaigner.bounce.resend.cancelJob')
		    ,handler: this.deleteJob
		});
	    }
	    else {
		m.push({
		    text: _('campaigner.bounce.resend.deleteJob')
		    ,handler: this.deleteJob
		});
	    }
	}
	if (m.length > 0) {
            this.addContextMenuItem(m);
        }
    }
    ,deleteJob: function() {
	var cs = this.getSelectedAsList();
        
        if (cs === false) { 
	    Ext.Msg.alert("Notification", "FALSE");
	    return false
	};
	
	if (this.getSelectionModel().getCount() == 1) {
	    Ext.Msg.alert("Notification", "EIN ELEMENT: "+this.getSelectionModel().getSelected().get('state'));
            if(this.getSelectionModel().getSelected().get('state') == 0) {
		MODx.msg.confirm({
		    title: _('campaigner.bounce.resend.cancelTitle')
		    ,text: _('campaigner.bounce.resend.cancelConfirm')
		    ,url: Campaigner.config.connector_url
		    ,params: {
			action: 'mgr/bounce/deleteJob'
			,markedJobs: cs
		    }
		    ,listeners: {
			'success': {fn:this.refresh,scope:this}
		    }
		});  
	    }
	    else {
		MODx.msg.confirm({
		    title: _('campaigner.bounce.resend.removeTitle')
		    ,text: _('campaigner.bounce.resend.removeConfirm')
		    ,url: Campaigner.config.connector_url
		    ,params: {
			action: 'mgr/bounce/deleteJob'
			,markedJobs: cs
		    }
		    ,listeners: {
			'success': {fn:this.refresh,scope:this}
		    }
		});
	    }
	}
	else {
	    Ext.Msg.alert("Notification", "MEHRERE ELEMENTE");
	    MODx.msg.confirm({
		    title: _('campaigner.bounce.resend.multiRemoveTitle')
		    ,text: _('campaigner.bounce.resend.multiRemoveConfirm')
		    ,url: Campaigner.config.connector_url
		    ,params: {
			action: 'mgr/bounce/deleteJob'
			,markedJobs: cs
		    }
		    ,listeners: {
			'success': {fn:this.refresh,scope:this}
		    }
		});
	}
	return true;
    }
});
Ext.reg('campaigner-grid-bounce-resend',Campaigner.grid.Resend);

Campaigner.grid.SoftDetail = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/bounce/getSoftDetailList', subscriber_id: Ext.getCmp('act_subscriber_id').getValue() }
        ,fields: ['id', 'newsletterTitle', 'reason', 'sent_date', 'docid', 'code', 'newsletter']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        ,columns: [{
            header: _('campaigner.newsletter.subject')
            ,dataIndex: 'newsletterTitle'
	    ,renderer: this._renderNewsletter
            //,width: 10
        },{
            header: _('campaigner.bounce.code')
            ,dataIndex: 'code'
            //,width: 10
        },{
            header: _('campaigner.bounce.reason')
            ,dataIndex: 'reason'
            //,width: 10
        },{
            header: _('campaigner.bounce.recieved')
            ,dataIndex: 'sent_date'
        }]
    });
    Campaigner.grid.SoftDetail.superclass.constructor.call(this,config)
};

Ext.extend(Campaigner.grid.SoftDetail,MODx.grid.Grid,{
    getMenu: function() {
	var m = [];
	m.push({
		text: _('campaigner.bounce.soft.detail.resend')
		,handler: this._resendNewsletter
	    });
	if (m.length > 0) {
            this.addContextMenuItem(m);
        }
    }
    ,_resendNewsletter: function(e) {
	/*this.updateWindow = MODx.load({
	    xtype: 'campaigner-window-resendNewsletter'
	    ,record: this.menu.record
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        this.updateWindow.setValues(this.menu.record);
        this.updateWindow.show(e.target);*/
	MODx.msg.confirm({
            title: _('campaigner.bounce.soft.resendTitle')
            ,text: _('campaigner.bounce.soft.resendConfirm')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/bounce/resendNewsletter'
                ,subscriber: Ext.getCmp('act_subscriber_id').getValue()
		,newsletter: this.menu.record.newsletter
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,_renderNewsletter: function(value, p, rec) {
	return '<a href="?a=30&id='+ rec.data.docid +'">'+ value +'</a>';
    }
});
Ext.reg('campaigner-grid-bounce-softDetail',Campaigner.grid.SoftDetail);

/*
 ######################################################
    WINDOWS
 ######################################################
*/
Campaigner.window.Reactivate = function(config) {
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
        title: _('campaigner.subscriber')
        ,id: this.ident
        ,height: 400
        ,width: 475
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/bounce/reactivateSubscriber'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'subscriber'
            ,id: this.ident +'-id'
        },{
            xtype: 'hidden'
            ,name: 'groups'
            ,id: this.ident +'-groups'
        },{
	    tag: 'p'
	    ,cls: 'window-description'
	    ,html: _('campaigner.hard.reactivate.description')
	},{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.subscriber.email')
            ,name: 'email'
            ,id: 'campaigner-'+this.ident+'-email'
        }, {
	    tag: 'div'
	    ,cls: 'window-description'
	    ,html: _('campaigner.hard.reactivate.groupDescription')
	}]
    });
    Campaigner.window.Reactivate.superclass.constructor.call(this,config);
    
    
    this.addListener('show', function(cmp) {
	var id = null;
	if(cmp.record) id = cmp.record.subscriber;
	    //Ext.Msg.alert("Notification", id);
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
					if(item.id == i[0]) {
					    //Ext.Msg.alert("Notification",item.id + "==" + i[0]);
					    checked = true;
					}
				    });
				}
				if(checked) {
				    this.items.items[0].add({
					tag: 'div'
					,cls: 'subscriber-window-groups-header'
					,html: '<span style="color: ' + item.color + '; font-size:0.8em;">' + item.name + '</span>'
				    });    
				}				
			    }, this);
			}
		       this.doLayout(false, true);
		    }, scope: this }
		}
	    });
		
        }, this);
};
Ext.extend(Campaigner.window.Reactivate,MODx.Window);
Ext.reg('campaigner-window-reactivate',Campaigner.window.Reactivate);

Campaigner.window.SoftDetail = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    /*this.gpstore = new Ext.data.Store({
	proxy: new Ext.data.HttpProxy({url: Campaigner.config.connector_url, method:'POST'})
	,baseParams: { action: 'mgr/bounce/getSoftDetailList', subscriber_id: Ext.getCmp('act_subscriber_id').getValue() }
	,reader: new Ext.data.JsonReader({
	    root: 'results',
	    fields: [ {name: 'id'},{name: 'newsletterTitle'}, {name: 'docid'}, {name: 'reason'}, {name: 'sent_date'}] 
	})
    });*/
    Ext.applyIf(config,{
        title: _('campaigner.subscriber')
        ,id: this.ident
        ,height: 400
        ,width: 775
        ,url: Campaigner.config.connector_url
        //,action: 'mgr/bounce/reactivateSubscriber'
        ,fields: [{
            xtype: 'hidden'
	    ,name: 'subscriber'
            ,id: 'act_subscriber_id'
        },{
            xtype: 'hidden'
	    ,name: 'newsletter'
            ,id: 'act_newsletter_id'
        },{
	    tag: 'p'
	    ,cls: 'window-description'
	    ,html: _('campaigner.bounce.soft.detail.info')
	},{
	    xtype: 'campaigner-grid-bounce-softDetail'
	    ,id: 'campaigner-grid-bounce-softDetail'
	    ,preventRender: true
	}]
	,buttons: [{
	     text: _('close')
            ,scope: this
            ,handler: function() { this.hide(); }
	}]
    });
    Campaigner.window.SoftDetail.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.SoftDetail,MODx.Window);
Ext.reg('campaigner-window-softDetail',Campaigner.window.SoftDetail);


/*
 ######################################################
    ALTES NICHT MEHR VERWENDETES RESEND WINDOW
 ######################################################
*/
/*
Campaigner.window.Resend = function(config) {
     config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    Ext.applyIf(config,{
        title: _('campaigner.newsletter.properties')
        ,id: this.ident
        ,height: 400
        ,width: 475
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/bounce/resendNewsletter'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: 'campaigner-'+this.ident+'-id'
        },{
            xtype: 'hidden'
            ,name: 'subscriber'
            ,id: 'subscriber-'+this.ident+'-id'
        },{
	    tag: 'p'
	    ,cls: 'window-description'
	    ,html: _('campaigner.soft.detail.resend.description')
	},{
            xtype: 'datefield'
            ,fieldLabel: _('campaigner.bounce.date')
            ,name: 'date'
	    ,format: 'd.m.Y'
            ,id: 'campaigner-'+this.ident+'-date'
        },{
            xtype: 'timefield'
            ,fieldLabel: _('campaigner.autonewsletter.time')
            ,name: 'time'
            ,id: 'campaigner-'+this.ident+'-time'
            ,format: 'H:i:s'
        }]
    });
    Campaigner.window.Resend.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.Resend,MODx.Window);
Ext.reg('campaigner-window-resendNewsletter',Campaigner.window.Resend);
*/

/*
 ######################################################
    ALTE NICHT MEHR VERWENDETE BOUNCE GRID FUNKTION
 ######################################################
*/
/*Campaigner.grid.Bounce = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/bounce/getList' }
        ,fields: ['id', 'firstname', 'lastname', 'email', 'newsletterTitle', 'reason', 'date', 'docid', 'state', 'type']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        ,columns: [{
            header: _('campaigner.bounce.firstname')
            ,dataIndex: 'firstname'
            //,width: 10
        },{
            header: _('campaigner.bounce.lastname')
            ,dataIndex: 'lastname'
            //,width: 10
        },{           
	    header: _('campaigner.bounce.email')
            ,dataIndex: 'email'
            //,width: 200
        },{
            header: _('campaigner.bounce.newsletter')
            ,dataIndex: 'newsletterTitle'
            //,width: 300
	    ,renderer: this._renderNewsletter
        },{
            header: _('campaigner.bounce.reason')
            ,dataIndex: 'reason'
            //,width: 150
        },{
            header: _('campaigner.bounce.type')
            ,dataIndex: 'type'
            //,width: 30
        },{
            header: _('campaigner.bounce.date')
            ,dataIndex: 'date'
           // ,width: 100
        },{
            header: _('campaigner.bounce.state')
            //,width: 250
	    ,renderer: this._renderState
        }]
	,defaults: {
	    sortable: true,
	    menuDisabled: true,
	    width: 50
	}
    });
    Campaigner.grid.Bounce.superclass.constructor.call(this,config)
};

Ext.extend(Campaigner.grid.Bounce,MODx.grid.Grid,{
    _renderNewsletter: function(value, p, rec) {
	return '<a href="?a=30&id='+ rec.data.docid +'">'+ value +'</a>';
    }
    ,_renderState: function(value, p, rec) {
	return rec.data.state;
    }
    ,getMenu: function() {
	var m = [];
	m.push({
	    text: _('campaigner.bounce.dontDeactivate')
	    ,handler: this.dontDeactivate
	});
	m.push('-');
	m.push({
	    text: _('campaigner.bounce.deactivateNow')
	    ,handler: this.deactivateNow
	});
	
        if (m.length > 0) {
            this.addContextMenuItem(m);
        }
	return m;    
    }
    ,dontDeactivate: function() {
	MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/bounce/dontDeactivate'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.refresh();
                },scope:this}
            }
        });
    }
});
Ext.reg('campaigner-grid-bounce',Campaigner.grid.Bounce);
*/