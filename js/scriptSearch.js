$(document).ready(function() {
    $('input[type="checkbox"]').change(function() {
        var filters = {};
        $('input[type="checkbox"]:checked').each(function() {
            var name = $(this).attr('name');
            if (name) {
                filters[name] = 'on';
            }
        });
        $.ajax({
            type: 'POST',
            url: 'notes/search_service',
            data: filters,
            dataType: 'json',
            success: function(response) {
                $('.notes_no').html('');
                $('.notes_personal').html('');
                $('.notes_shared').html('');
                let personal_notes = response.notes_searched['personal'];
                let shared_notes = response.notes_searched['shared'];
                if (personal_notes.length > 0) {
                    show_notes(personal_notes, "Your notes:", titlePage, response.list_filter_encoded,".notes_personal");
                }
                if (!isEmptyObject(shared_notes)) {
                    for (const user_shared in shared_notes) {
                        let notes_shared_by_user = shared_notes[user_shared];
                        show_notes(notes_shared_by_user, "Notes shared by " + user_shared + ":", titlePage, response.list_filter_encoded,".notes_shared", true);
                    }
                }
                if (!personal_notes.length > 0 && isEmptyObject(shared_notes)) {
                    $('.notes_no').html('<h4 class="title-note">No note matches.</h4>');
                }
                if (response.list_filter_encoded != null && response.list_filter_encoded.trim() !== '') {
                    var baseUrl = 'http://localhost/prwb_2324_a04/notes/search';
                    var newUrl = baseUrl + '/' + response.list_filter_encoded;
                    history.pushState(null, null, newUrl);
                } else {
                    var baseUrl = 'http://localhost/prwb_2324_a04/notes/search';
                    var currentUrl = window.location.href;
                    var urlParts = currentUrl.split('/');
                    if (urlParts[urlParts.length - 1].match(/^[0-9a-zA-Z]+$/)) {
                        urlParts.pop();
                    }
                    var newUrl = urlParts.join('/');
                    history.pushState(null, null, newUrl);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                console.error(status)
                console.error(error)
            }
        });
    });
});

function isEmptyObject(obj) {
    for (var key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
            return false;
        }
    }
    return true;
}
function show_notes(arrNotes, title, titlePage, param, sectionClass, append = false) {
    var html = '';
    html += '<h4 class="title-note">' + title + '</h4>';
    var isParamExist = !!param;
    html += '<ul id="sortable" class="list-note connectedSortable">';

    var labelPromises = arrNotes.map(function(note) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: 'POST',
                url: 'notes/get_labels_service',
                data: {note_id: note.id},
                dataType: 'json',
                success: function(response) {
                    resolve(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching labels:', error);
                    reject();
                }
            });
        });
    });

    Promise.all(labelPromises)
        .then(function(labelResponses) {
            arrNotes.forEach(function(note, index) {
                var openNoteUrl = "./Notes/open_note/" + note.id;
                if (isParamExist) {
                    openNoteUrl += "/" + param;
                }

                html += '<li id="' + note.id + '" class="note ui-state-defaultui-state-default">';
                html += '<a href="' + openNoteUrl + '" class="link-open-note">';
                html += '<div class="header-in-note">' + note.title + '</div>';
                html += '<div class="body-note">';
                if (note.content) {
                    var maxLg = 75;
                    var contentSub = note.content.length > maxLg ? note.content.substring(0, maxLg) + "..." : note.content;
                    html += '<p class="card-text mb-0">' + contentSub + '</p>';
                } else {
                    var items = note.list_item;
                    var listItemShowable = items.length > 3 ? items.slice(0, 3) : items;
                    listItemShowable.forEach(function(item) {
                        var maxLg = 15;
                        var contentSub = item.content.length > maxLg ? item.content.substring(0, maxLg) + "..." : item.content;
                        html += '<div class="form-check">';
                        html += '<input class="form-check-input cursor-pointer" type="checkbox" value=""' + (item.checked ? ' checked' : '') + ' disabled>';
                        html += '<label class="form-check-label cursor-pointer">' + contentSub + '</label>';
                        html += '</div>';
                    });
                    if (items.length > 3) {
                        html += '<p class="card-text">...</p>';
                    }
                }
                html += '</div>';
                html += '<div class="form-check">';
                var labels = labelResponses[index];
                if (labels && labels.length > 0) {
                    html += '<form action="notes/edit_labels/' + note.id + '" method="POST" class="navbar-brand d-inline-block">';
                    html += '<input type="hidden" name="note_id" value="' + note.id + '">';
                    html += '<button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">';
                    html += '<i class="bi bi-tag"></i>';
                    html += '</button>';
                    html += '</form>';
                    labels.forEach(function(label) {
                        html += '<span class="badge rounded-pill bg-secondary opacity-50">' + label.labelName + '</span>';
                    });
                }
                html += '</div>';
                html += '</a>';
                html += '</li>';
            });

            if (append) {
                $(sectionClass).append(html);
            } else {
                document.querySelector(sectionClass).innerHTML = html;
            }
        })
        .catch(function() {
            console.error('Une ou plusieurs requêtes AJAX ont échoué.');
        });
}

