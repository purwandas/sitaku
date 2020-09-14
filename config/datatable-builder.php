<?php

return [
	'buttonTemplates' => [
		'test' => '<button class="btn btn-sm btn-primary" title="TEST" data-tooltip="tooltip" data-placement="top">TEST</button>',

		'customPage' => '<a href="<<url>>" class="<<button>>" style="cursor: pointer;" data-tooltip="tooltip" data-placement="right" title="<<title>>" #attributes#><<icon>></a>',

		'custom' => '<button class="<<button>>" data-tooltip="tooltip" data-placement="right" title="<<title>>" style="cursor: pointer; text-decoration: none;" #attributes#><<icon>></button>',

		'editPage' => '<a href="<<url>>" class="btn btn-sm btn-datatable btn-primary" style="cursor: pointer;" data-tooltip="tooltip" data-placement="right" title="Ubah"><<icon>></a>',

		'edit' => '<button onclick=<<onclick>> title="Ubah" class="btn btn-sm btn-datatable btn-primary" data-target="<<data-target>>" data-toggle="modal" data-tooltip="tooltip" data-placement="right"><<icon>></button>',

		'delete' => '<button data-url=<<data-url>> title="Hapus" class="btn btn-sm btn-datatable btn-danger js-swal-delete" data-tooltip="tooltip" data-placement="right"><<icon>></button>',
	]
];