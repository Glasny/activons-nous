$(document).ready(function() {
	// Affichage édition de commentaire
	$('.editComment').click(function() {
		var loc = $(this).parent().hide().siblings('.commentTexte').eq(0);
		var text = loc.text().replace(/<br><\/br>/, "\n");
		loc.hide();
		loc.next().show().find('textarea').val(text);
	});
	// Annuler édition
	$('.cancelEdit').click(function() {
		var loc = $(this).parent().parent();
		loc.hide();
		loc.siblings('.commentTexte').eq(0).show().siblings('.commentPied').eq(0).show();
	});
});

// Confirmer suppression du commentaire
function confirmSupp(form) { 
	if (confirm('Veuillez confirmer la suppression : '))
		return true;
	else
		return false;
}
