(function($) {
	var program = {};
	program.size;
	var form1 = {};
	program.headerImage = new Image();
	$(document).ready(function () {	
		// add validations
		form1.addValidatonAjaxForm();
		// header image
		var winWidth = $(window).width();
		var size = "";
		if(winWidth >= 1800) {
			// tv
			size = "2400";	
		}
		if(winWidth >= 960) {
			// desktop
			size = "960";
		}
		else if(winWidth >= 480) {
			// tablet
			size = "480";
		}
		else {			
			size = "320";
		}
		program.size = size;
		program.headerImage.src = "images/header-" + size + ".jpg";
		$("#loading").attr("style", "display: block;");
	});
	// bind image
	$(program.headerImage).load(function () {
		if($(".header-image").css('background-image') == 'none') {			
			$("#loading").attr("style", "display: none;");
			$(".header-image").css({"background-image": "url('" + this.src + "')", "opacity": 1, "height": this.height + "px", "width": this.width + "px"});
			window.setTimeout(function() { $(".header-image").addClass("drop-shadow "); }, 1000);
		}
	});	
	$(window).ready(function () {
		
	});
	$(window).resize(function () {

	});
	$(window).scroll(function () {	
		
	}); 
	
	/* form logic */
	form1.submitAjaxForm = function() {
		var name = $("#name").val();
		var lastname = $("#lastname").val();
		var database = $("#database").val();
		var file = $("#file").val();
		var xyname = $("#xyname").val();
		var xyposition = $("#xyposition").val();
		
		// TODO: check name and last name exists
		$.ajax({
			type: "POST",
			url: "controller.php?name=" + name + "&lastname=" + lastname + "&database=" + database,	
			success: function(data) {
				if (data == undefined || data == "") {
					if(data.responseText != undefined) {
						console.log(data.responseText);
					}
					return;
				}				
				if(data == "not found"){
					program.showAlert("Usuario no existe", "Por favor revisar el Nombre y Apellido ingresados", "pastel-info");					
				}
				else {
					var names = data.split(",");
					window.location = "download.php?name=" + encodeURIComponent(names[1]) + "&lastname=" + encodeURIComponent(names[0]) + "&file=" + file + "&xyname=" + xyname + "&xyposition=" + xyposition + "&position=" + encodeURIComponent(names[2]) + "&size=" + program.size;					
					program.showAlert("Diploma descargado", "Gracias por participar del VIII Enapol", "pastel-info"); 				
					form1.clearAjaxForm();					
				}
			},
			error: function(data, textStatus, jqXHR) {
				console.log(data.responseText);
			}
		});

		
		/*
		$.ajax({
			type: "POST",
			data: {
				name: $("[name='name']").val(), 			
				lastname: $("[name='lastname']").val()
			}, 
			url: "index.php?option=com_mailto&task=formSubmission&format=raw", // name of page and web method			
			success: function (data) {
				if (data == undefined || data == "") {
					if(data.responseText != undefined) {
						console.log(data.responseText);
					}
					return;
				}
				// TODO: download logic
				program.showAlert("Diploma descargado", "Muchas gracias por asistir a las Jornadas de la EOL", "pastel-info"); 				
				form1.clearAjaxForm();
			},
			error: function (data, textStatus, jqXHR) {
				if(data.responseText != undefined) {
					console.log(data.responseText);
				}
			}
		});
		*/
	}

	// clear all ajax form inputs
	form1.clearAjaxForm = function() {
		$.each($("#form1").find("input:visible, textarea"), function(y, input) {
			$(input).val("");
		});
	}

	// add required validation to all ajax form inputs
	form1.addValidatonAjaxForm = function() {
		// jquery validation config
		$.validator.addMethod("required", function(value, element) {
			// check blank spaces
			if(value.trim().length == 0 && value != "")		
			  return value.indexOf(" ") < 0;
		  
			return !value.trim().length == 0;
		}, "Campo requerido" );
		$.validator.addMethod("email", function(value, element) {
			 return this.optional(element) || /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/.test(value);
		}, "Ingrese un email v&#225;lido." );
		
		$.each($("#form1"), function(x, form) {
			// add required to input
			$.each($(form).find("input:visible, textarea"), function(y, input) {
				if($(input).attr("notRequired") == undefined && $(input).attr("disabled") == undefined && $(input).attr("type") != "file" && $(input).attr("type") != "checkbox" && $(input).attr("type") != "radio") {
					$(input).addClass("required");
				}
			});
			// submit event
			$(form).find(".ajaxFormBtn").click(function() {    							
				var status = $(form).valid();
				if(status)
					form1.submitAjaxForm();
				return status;
			});	
			// add validation to form
			$(form).validate({				
				invalidHandler: function(form, validator) {  						
					var title = "Por favor revisar " + validator.numberOfInvalids();				
					if(validator.numberOfInvalids() > 1)
						title += " errores:\n\n";
					else
						title += " error:\n\n";
					var summary = '<ul>';
					var label;
					$.each(validator.errorMap, function(key, value) {  							
						label = $("input[name='" + key + "']");	
						if(label == undefined || label.length == 0) {
							label = $("textarea[name='" + key + "']");
						}
						if(label.attr("placeholder") != undefined && label.attr("placeholder").length > 0) {
							summary += '<li>' + label.attr("placeholder").replace(":", "") + '</li>';
							return true;
						}					
						if(label == undefined || label.length == 0)
							label = $("input[name='" + key + "").prev(); 
						if(label == undefined || label.length == 0 || label[0].nodeName.toLowerCase() != "label")
							label = $("input[name='" + key + "").parent().prev();
						if(label == undefined || label.length == 0 || label[0].nodeName.toLowerCase() != "label")
							label = $("label[for='" + key +"']").first();
						if(label == undefined || label.length == 0 || label[0].nodeName.toLowerCase() != "label")
							label = $("textarea[name='" + key + "']").parent().parent().find("label[for='" + key + "']").first();											
						else if(label != undefined && label.length > 0) {
						  summary += '<li><strong>' + label[0].innerHTML.replace(":", "") + '</strong>';					  
						  summary += ': ' + value + '</li>';
						}
					});
					summary += '</ul>';
					program.showAlert(title, summary, "pastel-warning");
				}
			});	
		});
	}
	
	// alert (Types: pastel-danger, pastel-info, pastel-warning
	program.showAlert = function(pTitle, pMsg, pType, pTime, pElement) {	
		var time = 5000;
		if(pTime != undefined) {
			time = parseInt(pTime);        
		}	  		
		var positionToElement = "body"; // default
		if(pElement != undefined) {
			positionToElement = pElement;
		}
		$.notify(
			{
				title: pTitle,
				message: pMsg
			},
			{
				type: pType,
				delay: time,
				z_index: 110000,
				element: positionToElement,
				template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
					'<span data-notify="title">{1}</span>' +
					'<span data-notify="message">{2}</span>' +
				'</div>'
			}
		); 
	}

})(jQuery);
