<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Крестики-нолики</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <!-- Styles -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/game.css') }}" rel="stylesheet" type="text/css">
        <!-- Script -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <script src="{{ asset('js/game.js') }}"></script>
    </head>
    <body>
      <div class="container">
        <div class="row row-10">
        </div>
        <div class="row">
          <div class="col-xs-6">
            <div class="row">
              <form id="step_form" action="{{ route('step_user') }}" method="post">
                <input type="hidden" name="step" id="step" value="">
                <input type="hidden" name="game_id" id="game_id" value="">
                {{ csrf_field() }}
              </form>
              <div class="col-xs-4 col-xs-offset-4">
                <button class="btn btn-primary col-xs-12" id="new_game">Начать новую игру</button>
              </div>
            </div>
            <div class="row row-10">
            </div>
            <div class="row">
              <div class="col-xs-4 col-xs-offset-4">
                <button class="btn btn-danger disabled col-xs-12" id="give_up">Сдаться</button>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-2 col-xs-offset-1 cell-pointer"></div>
              <div class="col-xs-2 cell-pointer">1</div>
              <div class="col-xs-2 cell-pointer">2</div>
              <div class="col-xs-2 cell-pointer">3</div>
            </div>
            <div class="row">
              <div class="col-xs-2 col-xs-offset-1 cell-pointer">A</div>
              <div class="col-xs-2 cell cell-disable" id="a-1"></div>
              <div class="col-xs-2 cell cell-disable" id="a-2"></div>
              <div class="col-xs-2 cell cell-disable" id="a-3"></div>
            </div>
            <div class="row">
              <div class="col-xs-2 col-xs-offset-1 cell-pointer">B</div>
              <div class="col-xs-2 cell cell-disable" id="b-1"></div>
              <div class="col-xs-2 cell cell-disable" id="b-2"></div>
              <div class="col-xs-2 cell cell-disable" id="b-3"></div>
            </div>
            <div class="row">
              <div class="col-xs-2 col-xs-offset-1 cell-pointer">C</div>
              <div class="col-xs-2 cell cell-disable" id="c-1"></div>
              <div class="col-xs-2 cell cell-disable" id="c-2"></div>
              <div class="col-xs-2 cell cell-disable" id="c-3"></div>
            </div>
            <div class="row row-10">
            </div>
            <div class="row">
              <div class="col-xs-12" id="status">

              </div>
            </div>

          </div>
          <div class="col-xs-5">
            <div class="row">
              <div class="col-xs-12">
                <table class="table table-striped table-bordered table-hover" id="win">
                    <thead>
                      <th class="sorting_asc" tabindex="0" aria-controls="sample_3" rowspan="1" colspan="1">
                        Победы
                      </th>
                      <th class="sorting" tabindex="1" aria-controls="sample_3" rowspan="1" colspan="1">
                        Поражения
                      </th>
                    </thead>
                    <tbody id="">
                      <tr>
                        <td><a href="#" id="user" class="a_result">0</a></td>
                        <td><a href="#" id="pc" class="a_result">0</a></td>
                      </tr>
                    </tbody>
                </table>
              </div>
            </div>
            <div class="row row-10">
            </div>
            <div class="row">
              <div class="col-xs-12">
                <table class="table table-striped table-bordered table-hover" id="list_step">
                    <thead>
                      <th>
                        #
                      </th>
                      <th>
                        Дата время
                      </th>
                      <th>
                        Ходы
                      </th>
                    </thead>
                    <tbody id="">
                    </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </body>
</html>
