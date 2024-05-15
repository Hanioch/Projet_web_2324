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
                if (response.notes_searched.personal.length > 0) {
                    afficherNotes(response.notes_searched.personal, "Vos notes :", "Search my notes");
                }

                for (const utilisateur in response.notes_searched.shared) {
                    const notesPartagees = response.notes_searched.shared[utilisateur];
                    const titrePartage = "Notes partagées par " + utilisateur + " :";
                    afficherNotes(notesPartagees, titrePartage, "Search my notes");
                }

                if (response.notes_searched.personal.length === 0 && Object.values(response.notes_searched.shared).every(notes => notes.length === 0)) {
                    const aucunElement = document.createElement('h4');
                    aucunElement.classList.add('title-note');
                    aucunElement.textContent = 'Aucune note ne correspond.';
                    const container = document.querySelector('.notes-section');
                    container.appendChild(aucunElement);
                }
               /* var currentUrl = window.location.href;

                var newUrl = currentUrl + 'ok';

                window.location.href = newUrl;*/
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                console.error(status)
                console.error(error)
            }
        });
    });
});
function afficherNotes(arrNotes, titre, titrePage) {
    const container = document.querySelector('.notes-section');
    container.innerHTML = ''; // Effacer le contenu précédent

    if (arrNotes.length > 0) {
        const titreElement = document.createElement('h4');
        titreElement.classList.add('title-note');
        titreElement.textContent = titre;
        container.appendChild(titreElement);

        arrNotes.forEach(note => {
            const noteElement = document.createElement('li');
            noteElement.id = note.id;
            noteElement.classList.add('note');
            noteElement.classList.add('ui-state-default');

            const lien = document.createElement('a');
            lien.href = `./Notes/open_note/${note.id}`;
            lien.classList.add('link-open-note');
            noteElement.appendChild(lien);

            const enTete = document.createElement('div');
            enTete.classList.add('header-in-note');
            enTete.textContent = note.titre;
            lien.appendChild(enTete);

            const corps = document.createElement('div');
            corps.classList.add('body-note');
            lien.appendChild(corps);

            const contenu = document.createElement('p');
            contenu.classList.add('card-text');
            contenu.classList.add('mb-0');
            const longueurMaxContenu = 75;
            contenu.textContent = note.contenu ?
                (note.contenu.length > longueurMaxContenu ?
                    note.contenu.substring(0, longueurMaxContenu) + '...' :
                    note.contenu) : '';
            corps.appendChild(contenu);

            const conteneurLabel = document.createElement('div');
            conteneurLabel.classList.add('form-check');
            lien.appendChild(conteneurLabel);

            if (note.labels && note.labels.length > 0) {
                note.labels.forEach(label => {
                    const spanLabel = document.createElement('span');
                    spanLabel.classList.add('badge');
                    spanLabel.classList.add('rounded-pill');
                    spanLabel.classList.add('bg-secondary');
                    spanLabel.classList.add('opacity-50');
                    spanLabel.textContent = label.nom_label;
                    conteneurLabel.appendChild(spanLabel);
                });
            }
            container.appendChild(noteElement);
        });
    } else {
        const elementAucuneCorrespondance = document.createElement('h4');
        elementAucuneCorrespondance.classList.add('title-note');
        elementAucuneCorrespondance.textContent = 'Aucune note ne correspond.';
        container.appendChild(elementAucuneCorrespondance);
    }
}