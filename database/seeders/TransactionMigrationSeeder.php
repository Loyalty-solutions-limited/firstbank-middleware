<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transactions')->insert([
            'member_reference' =>   'R00487924',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '10',
            'amount'    =>  123.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330433086',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01855098',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '16',
            'amount'    =>  10.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5010067468',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R00862795',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5010073027',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R07810224',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '19',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5010323698',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R05263192',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '14',
            'amount'    =>  66.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050184314',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04094207',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  120,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050152113',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R00515049',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  19,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050237698',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04196801',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '6',
            'amount'    =>  70.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333732254',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03645830',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '9',
            'amount'    =>  129,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5070121157',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03132868',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  127,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5070090730',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R05700673',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '11',
            'amount'    =>  60,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6311919993',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04550247',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  120,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6370046733',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06788655',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '10',
            'amount'    =>  14,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6559647393',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03659837',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  17.89,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6172667259',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R07827812',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6975315005',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R07827812',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6173012014',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02135419',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6975492632',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R05151603',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6975492632',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06468706',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331164154',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06114418',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333501236',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06747309',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333862502',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02730089',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333782527',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02781117',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333923281',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02781117',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331864997',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06747113',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331926721',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02863228',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331926721',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01466266',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330970741',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02287819',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331989494',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03089205',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330204536',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01629718',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331384415',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06677715',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5332185774',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03486378',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330457978',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03712408',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333906439',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06511383',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5332537902',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06729244',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5332696175',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R05136637',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050207752',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R07190909',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050210420',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R07260010',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050180309',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04523183',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050222971',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02249231',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050224236',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02739683',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333185829',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02739683',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331328497',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04836220',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331874372',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06454985',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331874372',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R07659456',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333340327',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03639455',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333858538',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02782659',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5334095370',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03371646',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5332661337',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02316602',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331927962',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04053209',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5332440897',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03051419',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331420054',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04598928',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5332926292',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01824054',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5332156873',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02821241',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333221664',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04945494',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330759256',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04945494',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331960026',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R05782304',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333405396',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03271439',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333405396',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R07262110',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333688849',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R07262110',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5332351867',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02807792',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5334021043',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R00840057',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5334021043',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03066400',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331949599',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R05072579',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330758596',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02758495',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5332170088',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03350837',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333463992',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R05249419',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331904194',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02649391',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5332417125',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06923842',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333540567',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04602947',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331779958',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06438565',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333963942',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06455140',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333223974',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01694511',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333853588',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R00579125',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333858552',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03702657',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330559528',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01936809',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330984210',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03586714',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5010266003',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R00330820',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5010170184',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03378542',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330918257',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02956960',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5070119606',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04094207',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'PSOPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5010007514',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01112212',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5070119510',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01354441',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5070073926',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06290386',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050152113',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02473064',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050082946',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01484025',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBIT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330058784',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03189096',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6161883707',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R03837921',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6233756449',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02429804',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6160437134',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02137909',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6550827567',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04887697',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'POSPU',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6239849242',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01404007',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  'ONBPT',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6233499858',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R05066729',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '6231894514',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02145136',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5050176139',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R06668118',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330130244',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R02708429',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333461448',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01615991',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331177444',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R07154532',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5333903469',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R04790448',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5331839214',
            'status'    =>  1
        ]);
        DB::table('transactions')->insert([
            'member_reference' =>   'R01299674',
            'product_code'  =>  'MOBA',
            'quantity'  =>  '12',
            'amount'    =>  12.98,
            'branch_code'   =>  '020',
            'transaction_reference'     =>  'ONBIT848393494_2023',
            'channel'   =>  'USSD',
            'transaction_type'  =>  '770TR',
            'transaction_log_id'    =>  '2023',
            'transaction_date'  =>  '2023-08-01',
            'cron_id'   =>  1,
            'account_number'    =>  '5330441632',
            'status'    =>  1
        ]);

    }
}
