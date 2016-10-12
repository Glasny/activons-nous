$(document).ready(function() {
	// Affichage formulaire connection
	$('#displayConnect').click(function() {
		var loc = $(this).siblings('#connectBloc').show();
	});
	// Masquer formulaire connection
	$('#cancelConnect').click(function() {
		var loc = $(this).parent().parent().hide();
	});
});
