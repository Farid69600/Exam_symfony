{% extends 'base.html.twig' %}

{% block title %}Hello Controller!{% endblock %}

{% form_theme formulaire 'bootstrap_5_layout.html.twig' %}

{% block body %}

{{ form_start(formulaire) }}
{{ form_row(formulaire.content) }}
<br>
<button type="submit" class="btn btn-success">Actualiser cette impression</button>
<br>

{{ form_end(formulaire) }}

{% endblock %}
