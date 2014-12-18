# Class: pgsql
#
#   This class installs the postgresql server and client software.
#   Further it configures pg_hba.conf to trus the local icinga user.
#
# Parameters:
#
# Actions:
#
# Requires:
#
# Sample Usage:
#
#   include pgsql
#
class pgsql {

  Exec { path => '/sbin:/bin:/usr/bin' }

  package { [
    'postgresql', 'postgresql-server'
  ]:
      ensure => latest,
  }

  exec { 'initdb':
    creates => '/var/lib/pgsql/data/pg_xlog',
    command => 'service postgresql initdb',
    require => Package['postgresql-server']
  }

  service { 'postgresql':
    ensure  => running,
    require => [Package['postgresql-server'], Exec['initdb']]
  }

  file { '/var/lib/pgsql/data/pg_hba.conf':
    content => template('pgsql/pg_hba.conf.erb'),
    require => [Package['postgresql-server'], Exec['initdb']],
    notify  => Service['postgresql']
  }
}
