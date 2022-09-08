/**
 * Quando la paginazione è in un form ne imposta il valore e ne fa il submit
 * @param {String} id 
 * @param {Number} val 
 */
function dbp_submit_pagination(form_el, val) {
    if (form_el == false) {
        console.warn ("The form is not present in the dom");
        return;
    }
    el = form_el.getElementsByClassName('js-dbp-page')[0];
    if (el != false) {
    el.value = val;
        dbp_submit_form_choose_mode(form_el);
    } else {
        console.warn ("The pagination is not inside the form :");
    }
}
/**
 * Invia la form della paginazione per il selected
 * @param {Dom} el 
 */
function dbp_submit_pagination_selected(el) {
    let form_el = gp_parents(el, 'form');
    if (form_el != false) {
        let opt = el.options[el.selectedIndex];
        form_el.getElementsByClassName('js-dbp-page')[0].value = opt.value;
        dbp_submit_form_choose_mode(form_el);
    } else {
        console.warn ("The form is not present in the dom");
    }
}

/**
 * Invia la form della paginazione per il selected
 * @param {Dom} el 
 */
 function dbp_submit_sorting(el, val) {
    let form_el = gp_parents(el, 'form');
    if (form_el != false) {
        form_el.getElementsByClassName('js-dbp-sorting')[0].value = val;
        dbp_submit_form_choose_mode(form_el);
    } else {
        console.error ("The form is not present in the dom");
    }
}

/**
 * Invia la form della paginazione per il selected
 * @param {Dom} el 
 */
function dbp_submit_simple_search(el) {
    let form_el = gp_parents(el, 'form');
    if (form_el != false) {
        form_el.getElementsByClassName('js-dbp-page').value = '';
        dbp_submit_form_choose_mode(form_el);
    } else {
        console.error ("The form is not present in the dom");
    }
}

function dbp_submit_clean_simple_search(el) {
    let form_el = gp_parents(el, 'form');
    form_el.querySelectorAll('.js-dbp-search-input').forEach(function (element) {
        element.value = '';
    });
    dbp_submit_simple_search(el);
}

/**
 * Decide se inviare il form in ajax o con il submit
 */
function dbp_submit_form_choose_mode(form_el) {
    if (form_el.classList.contains('js-dbp-send-ajax')) {
        dbp_submit_ajax(form_el);
    } else {
        form_el.submit();
    }
}

/**
 * Invia un form di ricerca in ajax
 * @param {DOM} form_el 
 */
function dbp_submit_ajax(form_el) {
    var data_post = new FormData();
    form_el.querySelectorAll('input[name]:not(:disabled):not([readonly]),select[name]:not(:disabled):not([readonly]),textarea[name]:not(:disabled):not([readonly])').forEach(function(el) {
        let tag = el.tagName.toUpperCase();
        if (tag == "INPUT" || tag == "TEXTAREA") {
            let name = el.getAttribute('name');
            let value = el.value;
            data_post.append(name,value);
        }
        if (tag == "SELECT") {
            let name = el.getAttribute('name');
            let value = el.options[el.selectedIndex].value;
            data_post.append(name,value);
        }
    });
    preload.addIn(form_el);
    
    data_post.append('action','dbp_get_list');
    
    fetch(dbp_post, { method: "POST", body: data_post }) 
    .then((response) => response.json())
    .then((responseData) => {
      el_box =  document.getElementById(responseData.div);
      while(el_box.firstChild) {
        el_box.removeChild(el_box.firstChild);
      }
      el_box.innerHTML  = responseData.html;
      setup_dbp_popup(el_box);
      setup_dbp_load_ajax_custom_div(el_box);
    })
    .catch(error => console.warn(error));  
    
}


/**
 * Cerca sui livelli superiori fino a trovare il nodo richiesto.
 * @param {Dom element} el 
 * @param {String} nodeName 
 * @returns DOM|False
 */
function gp_parents(el, nodeName) {
    let parent_el = el.parentNode;
    while (parent_el && parent_el.nodeName.toUpperCase() != nodeName.toUpperCase()) {
        parent_el = parent_el.parentNode;
    }
    if (parent_el && parent_el.nodeName.toUpperCase() == nodeName.toUpperCase()) {
        return parent_el;
    } else {
        return false;
    }
}


/**
 * Verifica tutti i link che hanno la classe js-dbp-popup e ne intercetta il link
 */
function setup_dbp_popup(el_container) {
    el_container.querySelectorAll('.js-dbp-popup').forEach(function(el) {
        if (el.__dbp_data_popup_href == undefined) {
            el.__dbp_data_popup_href = el.getAttribute('href');
            el.removeAttribute('target');
            el.setAttribute('href', "javascript: void(0)");
            
            el.addEventListener('click', (e) => { 
                let classList = e.currentTarget.className.split(' ');
                let addCustomClass = "";
                for (i in classList) {
                    if (classList[i].indexOf('js-dbp-popup-mode') > -1) {
                        addCustomClass = classList[i].replace('js-dbp-popup-mode-', '');
                    }
                }
                //
                console.log (" ADD CLASS " + addCustomClass);

                dbp_popup.open(addCustomClass);
                e.stopPropagation();

                href = e.currentTarget.__dbp_data_popup_href;
             
                fetch(href, { method: "GET" }) 
                .then((response) => response.text())
                .then((responseData) => {
                    dbp_popup.content(responseData);
                });
                return false;
            });
        }
    });
}

/**
 * Verifica tutti i link che hanno la classe js-dbp-load-ajax-custom-div e ne intercetta il link. Questo metodo non è usato direttamente, ma è possibile settarlo da apply_filter.
 */
 function setup_dbp_load_ajax_custom_div(el_container) {
    el_container.querySelectorAll('.js-dbp-load-ajax-custom-div').forEach(function(el) {
        if (el.__dbp_data_load_ajax_custom_div == undefined) {
            el.__dbp_data_load_ajax_custom_div = el.getAttribute('href');
            el.removeAttribute('target');
            el.setAttribute('href', "javascript: void(0)");

            el.addEventListener('click', (e) => {
                div = document.getElementById('dbp_div_target_link');
                div.innerHTML = '';
                preload.addIn(div);
                e.stopPropagation();
                href = el.__dbp_data_load_ajax_custom_div;
               // alert(href);
                fetch(href, { method: "GET" }) 
                .then((response) => response.text())
                .then((responseData) => {
                    div = document.getElementById('dbp_div_target_link');
                    div.innerHTML = responseData;
                });
                return false;
            });
        }
    });
}

/**
 * v.1
 * Gestione del popup
 */
 dbp_popup = {
    // Apre il popup
    open:function(add_custom_class) {
        let popup = document.querySelector(".js-popup-box");
        if (popup == null) {
            popup = document.createElement('div');
            if (add_custom_class != '') {
                add_custom_class = ' dbp-popup-'+add_custom_class;
            }
            popup.className = 'dbp-popup-box js-popup-box dbp-fade'+add_custom_class;
            popup.innerHTML = '<div class="dbp-popup-dialog js-popup-dialog"><div class="dbp-popup-close js-popup-close">&times;</div><div class="dbp-popup-content js-popup-content"></div></div>';
            preload.addIn(popup.querySelector(".js-popup-content"));
            popup.addEventListener("click", (e) => {
                dbp_popup.close();
            });
            popup.querySelector('.js-popup-close').addEventListener("click", (e) => {
                dbp_popup.close();
            });
            document.body.prepend(popup);
            setTimeout(() => { document.querySelector(".js-popup-box").classList.add('dbp-popup-box-opacity') }, 100);
            popup.querySelector(".js-popup-dialog").addEventListener("click", (e) => { 
                e.stopPropagation();
            });
           
        }
        document.body.classList.add('dbp-overflow-hidden');
    },
    // Chiude il popup
    close:function() {
       document.querySelector(".js-popup-box").classList.remove('dbp-popup-box-opacity');
        setTimeout(() => {
            let popup = document.querySelector(".js-popup-box");
            if (popup != null) {
                popup.remove();
            }
            document.body.classList.remove('dbp-overflow-hidden');
        }, 100);
    },
    // Aggiunge il contenuto al popup
    content:function(html, append = false) {
        preload.remove(document.querySelector(".js-popup-box"));
        let content = document.querySelector(".js-popup-box .js-popup-content");
      
        if (content != null && html != undefined) {
            if ( html instanceof Element) {
                if (!append) {
                    content.innerHTML = '';
                }
                content.appendChild(html);
            } else {
                if (!append) {
                    content.innerHTML = html;
                } else {
                    content.innerHTML += html;
                }
            }
          
        } 
        return content;
    }
}


preload = {
    addIn:function(el) {
        el.style.position = 'relative';
        el.style.minHeight = '30px';
        el.innerHTML +='<div class="dbp-loader js-dbp-loader"><div></div><div></div><div></div><div></div></div>';
    },
    remove:function(el) {
        el.querySelector('.js-dbp-loader').parentNode.removeChild( el.querySelector('.js-dbp-loader'));
    }
}

//
setup_dbp_popup(document);
setup_dbp_load_ajax_custom_div(document);

/**
 * ×	&times;	&#215;	Multiplication

‹	&lsaquo;	&#8249;	Single left angle quotation
›	&rsaquo;	&#8250;	Single right angle quotation
 */