

jQuery(document).ready(function() {

jQuery("body > div.col-sm-12.page-title")addClass(".student-color");    


var project=jQuery("#user-form input#edit-field-student-looking-for-project.form-checkbox").attr("checked") ? 1 : 0;
if(project==0){jQuery("div#field-project-details").hide();}

var dessertation=jQuery("#user-form input#edit-field-student-looking-for-dissertation.form-checkbox").attr("checked") ? 1 : 0;
if(dessertation==0){jQuery("div#edit-field-dessertation-wrapper").hide();}

var exam=jQuery("#user-form input#edit-field-student-looking-for-preparation-of-competative-exams.form-checkbox").attr("checked") ? 1 : 0;
if(exam==0){jQuery("div#edit-field-student-comp-exam-type-0").hide();}

});


//hide project details if project checkbox not selected in looking for option
jQuery("#user-form input#edit-field-student-looking-for-project.form-checkbox").on('change', function(event){ 
event.preventDefault();
    		if (jQuery("#user-form input#edit-field-student-looking-for-project.form-checkbox").is(":checked")) {    	
                jQuery("div#field-project-details").show();
            } else {            	
                jQuery("div#field-project-details").hide();
            }
});

//hide dissertation details if dissertation checkbox not selected in looking for option
jQuery("#user-form input#edit-field-student-looking-for-dissertation.form-checkbox").on('change', function(event){ 
event.preventDefault();
    		if (jQuery("#user-form input#edit-field-student-looking-for-dissertation.form-checkbox").is(":checked")) {    	
                jQuery("div#edit-field-dessertation-wrapper").show();
            } else {            	
                jQuery("div#edit-field-dessertation-wrapper").hide();
            }
});

//hide dissertation details if dissertation checkbox not selected in looking for option
jQuery("#user-form input#edit-field-student-looking-for-preparation-of-competative-exams.form-checkbox").on('change', function(event){ 
event.preventDefault();
    		if (jQuery("#user-form input#edit-field-student-looking-for-preparation-of-competative-exams.form-checkbox").is(":checked")) {    	
                jQuery("div#edit-field-student-comp-exam-type-0").show();
            } else {            	
                jQuery("div#edit-field-student-comp-exam-type-0").hide();
            }
});
