$(document).ready(function() {
    $('input[type="checkbox"]').change(async function() {
        let filters = {};
        $('input[type="checkbox"]:checked').each(function() {
            let name = $(this).attr('name');
            if (name) {
                filters[name] = 'on';
            }
        });
        try {
            let response = await $.ajax({
                type: 'POST',
                url: 'notes/search_service',
                data: filters,
                dataType: 'json'
            });

            $('.notes_no').html('');
            $('.notes_personal').html('');
            $('.notes_shared').html('');

            let personal_notes = response.notes_searched['personal'];

            if (personal_notes.length > 0) {
                await show_notes(personal_notes, "Your notes :", titlePage, response.list_filter_encoded, ".notes_personal");
            }

            let shared_notes = response.notes_searched['shared'];
            if (!isEmptyObject(shared_notes)) {
                let sortedSharedNotes = sortSharedNotes(shared_notes);
                for (const user_shared in sortedSharedNotes) {
                    let notes_shared_by_user = sortedSharedNotes[user_shared];
                    await show_notes(notes_shared_by_user, "Notes shared by " + user_shared + " :", titlePage, response.list_filter_encoded, ".notes_shared", true);
                }
            }
            updateURL(response);
        } catch (error) {
            console.error('Error fetching shared notes:', error);
        }
    });
});

function updateURL(response) {
    if (response.list_filter_encoded != null && response.list_filter_encoded.trim() !== '') {
        let baseUrl = 'http://localhost/prwb_2324_a04/notes/search';
        let newUrl = baseUrl + '/' + response.list_filter_encoded;
        history.pushState(null, null, newUrl);
    } else {
        let currentUrl = window.location.href;
        let urlParts = currentUrl.split('/');
        if (urlParts[urlParts.length - 1].match(/^[0-9a-zA-Z]+$/)) {
            urlParts.pop();
        }
        let newUrl = urlParts.join('/');
        history.pushState(null, null, newUrl);
    }
}

function isEmptyObject(obj) {
    for (let key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
            return false;
        }
    }
    return true;
}

function sortSharedNotes(shared_notes) {
    if (typeof shared_notes === 'object' && shared_notes !== null) {
        let userNames = Object.keys(shared_notes);
        userNames.toUpperCase().sort();
        let sortedSharedNotes = {};
        userNames.forEach(userName => {
            sortedSharedNotes[userName] = shared_notes[userName];
        });
        return sortedSharedNotes;
    } else {
        return {};
    }
}

function show_notes(arrNotes, title, titlePage, param, sectionClass, append = false) {
    return new Promise((resolve, reject) => {
        let html = '';
        html += '<h4 class="title-note">' + title + '</h4>';
        let isParamExist = !!param;
        html += '<ul id="sortable" class="list-note connectedSortable">';
        let labelPromises = arrNotes.map(function(note) {
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
                    let openNoteUrl = "./Notes/open_note/" + note.id;
                    if (isParamExist) {
                        openNoteUrl += "/" + param;
                    }

                    html += '<li id="' + note.id + '" class="note ui-state-defaultui-state-default">';
                    html += '<a href="' + openNoteUrl + '" class="link-open-note">';
                    html += '<div class="header-in-note">' + note.title + '</div>';
                    html += '<div class="body-note">';
                    if (note.content != null && note.content !== '') {
                        let maxLg = 75;
                        let contentSub = note.content.length > maxLg ? note.content.substring(0, maxLg) + "..." : note.content;
                        html += '<p class="card-text mb-0">' + contentSub + '</p>';
                    } else {

                        let items = note.list_item;
                        if (items && items.length > 0) {
                            let listItemShowable = items.length > 3 ? items.slice(0, 3) : items;
                            listItemShowable.forEach(function(item) {
                                let maxLg = 15;
                                let contentSub = item.content.length > maxLg ? item.content.substring(0, maxLg) + "..." : item.content;
                                html += '<div class="form-check">';
                                html += '<input class="form-check-input cursor-pointer" type="checkbox" value=""' + (item.checked ? ' checked' : '') + ' disabled>';
                                html += '<label class="form-check-label cursor-pointer">' + contentSub + '</label>';
                                html += '</div>';
                            });
                            if (items.length > 3) {
                                html += '<p class="card-text">...</p>';
                            }
                        } else {
                            html += '';
                        }
                    }
                    html += '</div>';
                    html += '<div class="form-check">';
                    let labels = labelResponses[index];
                    if (labels && labels.length > 0) {
                        html += '<form action="notes/edit_labels/' + note.id + '" method="POST" class="navbar-brand d-inline-block">';
                        html += '<input type="hidden" name="note_id" value="' + note.id + '">';
                        html += '<button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">';
                        html += '<i class="bi bi-tag"></i>';
                        html += '</button>';
                        html += '</form>';
                        labels.forEach(function(label) {
                            html += ' <span class="badge rounded-pill bg-secondary opacity-50">' + label.label_name + '</span>';
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
                resolve();
            })
            .catch(function() {
                console.error('Une ou plusieurs requêtes AJAX ont échoué.');
                reject();
            });
    });
}


