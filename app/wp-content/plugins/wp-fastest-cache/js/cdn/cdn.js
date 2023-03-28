var WpfcCDN = {
	values: {"name" : "", "cdnurl" : "", "originurl" : "", "file_types" : "", "keywords" : "", "excludekeywords" : ""},
	id : "",
	nonce: "",
	template_url : "",
	content : "",
	interval : false,
	init: function(obj){
		this.set_params(obj);
		this.open_wizard();
	},
	check_conditions: function(action, current_page_number){
		var self = this;

		if(action == "next"){
			if(current_page_number == 2){
				self.check_url_exist();
			}else{
				return true;
			}
		}
	},
	set_params: function(obj){
		this.id = obj.id;
		this.nonce = obj.nonce;
		this.template_url = obj.template_main_url + "/" + this.id + ".php";

		if(obj.values){
			this.values = obj.values;
		}
	},
	open_wizard: function(){
		var self = this;
		if(jQuery("#wpfc-modal-" + self.id).length == 0){
			self.load_template(function(){
				self.fill_integration_fields();
				self.set_buttons_action();
				self.click_event_add_new_keyword_button();
				self.add_new_keyword_keypress();

				if(self.id == "other" || self.id == "cloudflare"){
					self.show_page("next");
					self.hide_button("back");

				}
				
			});
		}
	},
	insert_keywords: function(modal, classname, keywords){
		var self = this;

		if(keywords){
			jQuery.each(keywords.split(","), function( index, value ) {
				jQuery('<li class="' + classname + '"><a class="keyword-label">' + value + '</a></li>').insertBefore(modal.find(".wpfc-add-new-keyword").closest("." + classname)).click(function(){
					jQuery(this).remove();
				});
			});
		}
	},
	fill_integration_fields: function(){
		var self = this;
		var modal;

		jQuery(self.values).each(function(i, e){
			modal = jQuery("#wpfc-wizard-" + e.id);

			modal.find("input#cdn-url").val(e.cdnurl);
			modal.find("select#cdn-url").val(e.cdnurl);
			modal.find("#origin-url").val(e.originurl);

			self.insert_keywords(modal, "keyword-item", e.keywords);
			self.insert_keywords(modal, "keyword-item-exclude", e.excludekeywords);

			if(e.file_types){
				modal.find(".wpfc-checkbox-list input[type='checkbox']").attr("checked", false);
				
				jQuery.each(e.file_types.split(","), function( index, value ) {
					modal.find("#file-type-" + value).attr("checked", true);
				});
			}

		});

	},
	add_new_keyword_keypress: function(){
		jQuery(".wpfc-textbox-con .fixed-search input").keypress(function(e){
			if(e.keyCode == 13){
				var keyword = jQuery(e.target).val();
				var keyword_type_class = jQuery(e.target).closest("li[class*='keyword-item']").attr("class");
				
				jQuery(".wpfc-textbox-con").hide();
				jQuery(e.target).val("");
				jQuery('<li class="' + keyword_type_class + '"><a class="keyword-label">' + keyword + '</a></li>').insertBefore(jQuery(e.target).closest("." + keyword_type_class)).click(function(){
					jQuery(this).remove();
				});
			}
		});
	},
	click_event_add_new_keyword_button: function(){
		jQuery(".wpfc-add-new-keyword").click(function(){
			jQuery(this).next(".wpfc-textbox-con").show();
			jQuery(this).next(".wpfc-textbox-con").find(".fixed-search input").focus();
		});
	},
	set_buttons_action: function(){
		var self = this;
		var action = "";
		var current_page, next_page, current_page_number;
		var modal = jQuery("#wpfc-modal-" + self.id);

		self.buttons();

		modal.find("button.wpfc-dialog-buttons").click(function(e){
			action = modal.find(e.currentTarget).attr("action");
			current_page_number = modal.find(".wpfc-cdn-pages-container div.wiz-cont:visible").attr("wpfc-cdn-page");

			if(action == "next"){
				if(self.check_conditions("next", current_page_number)){
					self.show_page("next");
				}
			}else if(action == "back"){
				self.show_page("back");
			}else if(action == "finish"){
				self.save_integration();
			}else if(action == "close"){
				Wpfc_Dialog.remove();
			}else if(action == "remove"){
				self.remove_integration();
			}else if(action == "pause"){
				self.pause_integration();
			}else if(action == "start"){
				self.start_integration();
			}
		});
	},
	start_integration: function(){
		var self = this;
		var modal = jQuery("#wpfc-modal-" + self.id);

		self.disable_button("start");

		jQuery.ajax({
			type: 'POST',
			dataType: "json",
			url: ajaxurl,
			data : {"action": "wpfc_start_cdn_integration", "id" : self.id},
		    success: function(res){
		    	self.show_button("pause");
		    	self.enable_button("pause");
		    	self.hide_button("start");

		    	jQuery("div[wpfc-cdn-name='" + self.id + "']").find("div.meta").removeClass("pause");

		    },
		    error: function(e) {
		    	self.enable_button("start");
		    	alert("unknown error");
		    }
		  });

	},
	pause_integration: function(){
		var self = this;
		var modal = jQuery("#wpfc-modal-" + self.id);

		self.disable_button("pause");

		jQuery.ajax({
			type: 'POST',
			dataType: "json",
			url: ajaxurl,
			data : {"action": "wpfc_pause_cdn_integration", "id" : self.id},
		    success: function(res){
		    	self.show_button("start");
		    	self.enable_button("start");
		    	self.hide_button("pause");

		    	jQuery("div[wpfc-cdn-name='" + self.id + "']").find("div.meta").addClass("pause");

		    },
		    error: function(e) {
		    	self.enable_button("pause");
		    	alert("unknown error");
		    }
		  });


	},
	remove_integration: function(){
		var self = this;
		var modal = jQuery("#wpfc-modal-" + self.id);

		self.disable_button("remove");

		jQuery.ajax({
			type: 'POST',
			dataType: "json",
			url: ajaxurl,
			data : {"action": "wpfc_remove_cdn_integration", "id" : self.id},
		    success: function(res){
		    	self.values = jQuery.grep(self.values, function (e, i) {
							    	if(e.id == self.id){
							    		return false;
							    	}
							    	return true;
								});


		    	self.enable_button("remove");
		    	jQuery("div.tab7 div[wpfc-cdn-name='" + self.id + "']").find("div.meta").removeClass("isConnected");
		    	Wpfc_Dialog.remove();
		    	console.log(res);
		    },
		    error: function(e) {
		    	self.enable_button("remove");
		    	alert("unknown error");
		    }
		  });
	},
	save_integration: function(){
		var self = this;
		var modal = jQuery("#wpfc-modal-" + self.id);
		
		self.buttons();
		self.values = {};
		self.values.id = self.id;
		
		self.disable_button("finish");

		if(modal.find("input#cdn-url").length == 1){
			self.values.cdnurl = modal.find("input#cdn-url").val();
		}else if(modal.find("select#cdn-url").length == 1){
			self.values.cdnurl = modal.find("select#cdn-url").val();
		}


		self.values.originurl = modal.find("input#origin-url").val();
		self.values.file_types = modal.find(".wpfc-checkbox-list input[type='checkbox']:checked").map(function(){return this.value;}).get().join(",");
		self.values.keywords = modal.find(".keyword-item-list li.keyword-item a.keyword-label").map(function(){return this.text;}).get().join(",");
		self.values.excludekeywords = modal.find(".keyword-item-list li.keyword-item-exclude a.keyword-label").map(function(){return this.text;}).get().join(",");
		
		
		jQuery.ajax({
			type: 'POST',
			dataType: "json",
			url: ajaxurl,
			data : {"action": "wpfc_save_cdn_integration", "nonce" : self.nonce, "values" : self.values, "file_types" : self.values.file_types, "keywords" : self.values.keywords, "excludekeywords" : self.values.excludekeywords},
		    success: function(res){
				jQuery("div[wpfc-cdn-name='" + self.id + "']").find("div.meta").addClass("isConnected");

				self.enable_button("finish");

				self.show_page("next");
		    	console.log(res);
		    },
		    error: function(e) {
		    	self.enable_button("finish");
		    	alert("unknown error");
		    }
		  });



	},
	enable_button: function(button_type){
		clearInterval(this.interval);

		let self = this;
		let modal = jQuery("#wpfc-modal-" + self.id);
		let button = modal.find(".wpfc-dialog-buttons[action='" + button_type + "']");

		button.attr("disabled", false);
		button.text(button.text().replace(/\.+$/, ""));
	},
	disable_button: function(button_type){
		let self = this;
		let modal = jQuery("#wpfc-modal-" + self.id);
		let button = modal.find(".wpfc-dialog-buttons[action='" + button_type + "']");
		let text = button.text();
		let dot = 0;

		button.attr("disabled", true);

		button.text(text + ".");

		self.interval = setInterval(function(){
			text = button.text();
			dot = text.match(/\./g);

			console.log(dot);
			console.log(button);

			if(dot){
				if(dot.length < 3){
					button.text(text + ".");
				}else{
					button.text(text.replace(/\.+$/, ""));
				}
			}else{
				button.text(text + ".");
			}
		}, 300);
	},
	check_url_exist: function(){
		var self = this;
		var modal = jQuery("#wpfc-modal-" + self.id);
		var cdn_url = modal.find("#cdn-url").val();
		var origin_url = modal.find("#origin-url").val();

		if(!cdn_url || !origin_url){
			if(!cdn_url){
				modal.find("#cdn-url").css("background-color", "red");
			}else{
				modal.find("#cdn-url").css("background-color", "white");
			}

			if(!origin_url){
				modal.find("#origin-url").css("background-color", "red");
			}else{
				modal.find("#origin-url").css("background-color", "white");
			}

			return;
		}else{
			modal.find("#cdn-url").css("background-color", "white");
			modal.find("#origin-url").css("background-color", "white");
		}

		modal.find("#cdn-url-loading").show();
		modal.find(".wpfc-cdn-pages-container div.wiz-cont:visible #cdn-url").nextAll("label").html("");
		jQuery.ajax({
			type: 'GET',
			dataType: "json",
			url: ajaxurl,
			data : {"action": "wpfc_check_url", "url" : cdn_url, "origin_url" : origin_url, "type" : WpfcCDN.id},
		    success: function(res){
		    	modal.find("#cdn-url-loading").hide();
		    	if(res.success){
		    		self.show_page("next");
		    		modal.find("#cdn-url").css("background-color", "white");
		    		modal.find("#origin-url").css("background-color", "white");
		    	}else{
		    		if(WpfcCDN.id == "cloudflare"){
		    			modal.find("label.wiz-error-msg").html(res.error_message);
		    		}else{
		    			modal.find(".wpfc-cdn-pages-container div.wiz-cont:visible #cdn-url").nextAll("label").html(res.error_message);
		    		}
		    	}
		    },
		    error: function(e) {
		    	modal.find("#cdn-url-loading").hide();
		    	alert("unknown error");
		    }
		});
	},
	show_page: function(type){
		var current_page = jQuery("#wpfc-modal-" + this.id).find(".wpfc-cdn-pages-container div.wiz-cont:visible");
		current_page.hide();

		if(type == "next"){
			current_page.next().show();
		}else if(type == "back"){
			current_page.prev().show();
		}
		this.buttons();
	},
	buttons: function(){
		var self = this;
		var current_page, next_pages;

		current_page = jQuery("#wpfc-modal-" + this.id).find(".wpfc-cdn-pages-container div.wiz-cont:visible");
		next_pages = current_page.nextAll(".wiz-cont");

		jQuery(".wpfc-dialog-buttons[action]").hide();

		jQuery(self.values).each(function(i, e){
			if(e.id == self.id){
				self.show_button("remove");

				if(typeof e.status != "undefined" && e.status == "pause"){
					self.show_button("start");
				}else{
					self.show_button("pause");
				}
			}
		});

		if(next_pages.length){
			if(next_pages.length > 1){
				self.show_button("next");
			}else if(next_pages.length == 1){
				self.show_button("finish");
			}

			if(jQuery("#wpfc-modal-" + this.id).find(".wpfc-cdn-pages-container div.wiz-cont:visible").attr("wpfc-cdn-page") > 1){
				if(current_page.attr("wpfc-cdn-page") == 2){
					if(self.id == "maxcdn"){
						self.show_button("back");
					}
				}else{
					self.show_button("back");
				}
			}
		}else{
			self.show_button("close");
		}

	},
	show_button: function(type){
		jQuery("#wpfc-modal-" + this.id + " .wpfc-dialog-buttons[action='" + type + "']").show();
	},
	hide_button: function(type){
		jQuery("#wpfc-modal-" + this.id + " .wpfc-dialog-buttons[action='" + type + "']").hide();
	},
	load_template: function(callbak){
		var self = this;

		jQuery.ajax({
			type: 'POST',
			dataType: "json",
			url: ajaxurl,
			data : {"action": "wpfc_cdn_template", "id": self.id},
		    success: function(res){
		    	jQuery("body").append(res.content);
		    	Wpfc_Dialog.dialog("wpfc-modal-" + self.id);
		    	callbak();
		    	jQuery("#revert-loader-toolbar").hide();
		    },
		    error: function(e) {
		    	alert("CDN Template Error");
		    }
		});
	}
};