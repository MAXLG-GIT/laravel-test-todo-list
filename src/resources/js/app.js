import './bootstrap';

import Tagify from '@yaireo/tagify';

//TODO add progressbar on AJAX loadings

// The DOM element you wish to replace with Tagify
const input = document.querySelector('input[name=tags]');
new Tagify(input);

const saveTaskBtn = document.querySelector('button[name=saveTask]');
const deleteTaskBtn = document.querySelector('button[name=deleteTask]');
const editTaskBtn = document.querySelector('button[name=editTask]');
const uploadImgBtn = document.querySelector('button[name=uploadImage]');
const imageInputForm = document.querySelector('input[name=image]');
const resizedImageInputForm = document.querySelector('input[name=resized_image]');
const taskList = document.querySelector('div[id=taskList]');
const tagsFilter = document.querySelectorAll('input[name=tagsFilter]');
const searchBtn = document.querySelector('button[name=searchBtn]');
const searchField = document.querySelector('input[name=searchField]');

if (saveTaskBtn)
    saveTaskBtn.addEventListener('click', function()
    {
        let formData = new FormData( this.closest('form') );
        sendAjaxRequest(window.location.origin+'/edit', JSON.stringify(Object.fromEntries(formData)), onSaveTask,
            {'Content-Type':'application/json'});
    });

if (uploadImgBtn)
    uploadImgBtn.addEventListener('click', function()
    {
        let imageInputData = new FormData();
        let imageInput = document.querySelector('input[id=imageInput]')
        imageInputData.append("file", imageInput.files[0]);
        sendAjaxRequest(window.location.origin+'/edit/upload-image', imageInputData, onUploadImage);
    });

if (deleteTaskBtn)
    deleteTaskBtn.addEventListener('click', function()
    {
        sendAjaxRequest(window.location.origin+'/delete-task', JSON.stringify(
            {'id': deleteTaskBtn.getAttribute('data-id')}), onDeleteTask,
            {'Content-Type':'application/json'});
    });



if (tagsFilter)
    tagsFilter.forEach(function(tagCheckbox) {
        tagCheckbox.addEventListener('change', function() {
            let checkedTagsArr = [];
            let checkedTags = document.querySelectorAll('input[name=tagsFilter]:checked')
            for (let i = 0; i < checkedTags.length; i++) {
                checkedTagsArr.push(checkedTags[i].value)
            }

            sendAjaxRequest(window.location.origin+'/tags-filter', JSON.stringify(
                    {'checked_tags_arr': checkedTagsArr}), onFilterTags,
                {'Content-Type':'application/json'});

        });
    });

if (searchBtn)
    searchBtn.addEventListener('click', function()
    {

        sendAjaxRequest(window.location.origin+'/search', JSON.stringify(
                {'search_string': searchField.value ?? ''}), onSearch,
            {'Content-Type':'application/json'});

    });

function sendAjaxRequest(url='', postData=null, callback, contentType = '')
{
    let headers = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
    }
    if (contentType) headers = _.merge(headers, contentType);
    if(url === '' || postData === undefined  || callback === '') return false;
    document.querySelector("div[class=messageWrapper]").innerHTML = '';
    fetch(url, {
        method: 'post',
        body: postData,
        headers: headers
    }).then((response) => {

        return (response.json());
    }).then((res) => {
        if (res.status === 201) {
            console.log(res);
        }
        callback(res);
    }).catch((error) => {
        console.log(error);
    })
}

function onSaveTask(data = '') {
    if (data.status) {
        document.querySelector('input[name=id]').value = data.id;
        window.location.href = window.location.origin;
    } else {
        renderPopup(data.message);
    }
}

function onUploadImage(data = '') {
    if (data.status) {
        imageInputForm.value = data.image;
        resizedImageInputForm.value = data.resizedImage;
        renderThumb(data.image, data.resizedImage, "div[class=thumbImageWrapper]");
        initDeleteImageBtn();
    } else {
        renderPopup(data.message);
    }
}

function onDeleteImage(data = '') {
    if (data.status) {
        imageInputForm.value = '';
        resizedImageInputForm.value = '';
        document.querySelector('div[class=thumbImageWrapper]').innerHTML = '';
    } else {
        renderPopup(data.message);
    }
}

function renderPopup(message = '') {
    document.querySelector("div[class=messageWrapper]").innerHTML = '<div class="alert alert-warning" role="alert">' +
        message +
        "</div>";
}

function renderThumb(imageLink = '', resizedImageLink = '', elementWrapper = 'body') {
    document.querySelector(elementWrapper).innerHTML = '<a href="' +
        window.location.origin + '/' + imageLink +
        '" target="_blank"><img src="' +
        window.location.origin + '/' + resizedImageLink +
        '" class="img-thumbnail mt-2" alt="" ></a><br/>' +
        '<button class="btn btn-sm btn-outline-secondary mt-2" type="button" name="deleteImage">Удалить</button>';
}

function initDeleteImageBtn() {
    let deleteImageBtn = document.querySelector("button[name=deleteImage]");
    deleteImageBtn.addEventListener("click", function() {
        sendAjaxRequest(window.location.origin+'/edit/delete-image', JSON.stringify({
                'image':imageInputForm.value,
                'image_resized':resizedImageInputForm.value}), onDeleteImage,
            {'Content-Type':'application/json'});
    });
}

function onDeleteTask(data = ''){
    if (window.location.pathname.length > 1) {
        window.location.href = window.location.origin;

    }else if (data.status) {
        document.querySelector('a[data-id="'+data.id+'"]').remove();
        deleteTaskBtn.setAttribute('disabled','disabled');
        editTaskBtn.setAttribute('disabled','disabled');
    } else {
        renderPopup(data.message);
    }
}

function onFilterTags(data = '')
{
    searchField.value = '';
    if (data.status) {
        taskList.innerHTML = data.content;
    } else {
        taskList.innerHTML = '';
    }
    addTodoListListeners();
    disableTodoListActions();
}
function onSearch(data = '')
{
    tagsFilter.forEach(el => {
        el.checked = false;
    });
    if (data.status) {
        taskList.innerHTML = data.content;
    } else {
        taskList.innerHTML = '';
    }
    addTodoListListeners();
    disableTodoListActions();
}

function addTodoListListeners()
{

    if (taskList && deleteTaskBtn && editTaskBtn)
        taskList.querySelectorAll( 'a' ).forEach(el => {
            el.addEventListener('click', function() {

                if (el.getAttribute('aria-selected') === 'true') {
                    deleteTaskBtn.removeAttribute('disabled');
                    deleteTaskBtn.setAttribute('data-id', el.getAttribute('data-id'));
                    editTaskBtn.removeAttribute('disabled');
                    editTaskBtn.setAttribute('onclick',
                        'location.href="'+ window.location.origin +'/edit/'+el.getAttribute('data-id') +'"');
                }else{
                    deleteTaskBtn.setAttribute('disabled','disabled');
                    editTaskBtn.setAttribute('disabled','disabled');
                }
            });
        });
}

function disableTodoListActions()
{
    //TODO create normal separate function to disable buttons
    if (deleteTaskBtn && window.location.pathname.length < 2){
        deleteTaskBtn.setAttribute('disabled','disabled');
        editTaskBtn.setAttribute('disabled','disabled');
    }
}


window.onload = function() {
    if (document.querySelector("button[name=deleteImage]") !== null)
        initDeleteImageBtn();


    addTodoListListeners();
    disableTodoListActions();
};
