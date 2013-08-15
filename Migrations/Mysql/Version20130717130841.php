<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;
use TYPO3\Flow\Utility\Files;
use TYPO3\Flow\Utility\MediaTypes;

/**
 * New Resource Management
 *
 * - remove the typo3_flow_resource_resourcepointer table
 * - remove the non-used content security configuration table
 * - remove the fileextension column
 * - add and fill new columns for typo3_flow_resource_resource
 */
class Version20130717130841 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("ALTER TABLE typo3_flow_resource_resource DROP FOREIGN KEY FK_B4D45B32A4A851AF");
		$this->addSql("ALTER TABLE typo3_flow_security_authorization_resource_securitypublis_861cb DROP FOREIGN KEY FK_234846D521E3D446");
		$this->addSql("ALTER TABLE typo3_flow_resource_resource DROP FOREIGN KEY typo3_flow_resource_resource_ibfk_1");
		$this->addSql("DROP TABLE typo3_flow_resource_publishing_abstractpublishingconfiguration");
		$this->addSql("DROP TABLE typo3_flow_resource_resourcepointer");
		$this->addSql("DROP TABLE typo3_flow_security_authorization_resource_securitypublis_861cb");
		$this->addSql("DROP INDEX IDX_B4D45B323CB65D1 ON typo3_flow_resource_resource");
		$this->addSql("DROP INDEX IDX_B4D45B32A4A851AF ON typo3_flow_resource_resource");

		$this->addSql("ALTER TABLE typo3_flow_resource_resource CHANGE resourcepointer sha1 VARCHAR(40) NOT NULL, ADD md5 VARCHAR(32) DEFAULT NULL, ADD collectionname VARCHAR(255) NOT NULL, DROP publishingconfiguration, DROP fileextension, ADD mediatype VARCHAR(100) DEFAULT NULL, ADD relativepublicationpath VARCHAR(255) NOT NULL, ADD filesize BIGINT(20) UNSIGNED DEFAULT NULL");

		$resourcesResult = $this->connection->executeQuery('SELECT persistence_object_identifier, resourcepointer AS sha1, filename FROM typo3_flow_resource_resource');
		while ($resourceInfo = $resourcesResult->fetch(\PDO::FETCH_ASSOC)) {
			$resourcePathAndFilename = FLOW_PATH_DATA . 'Persistent/Resources/' . $resourceInfo['sha1'];
			if (!file_exists($resourcePathAndFilename)) {
				throw new \Exception(sprintf('Error while migrating database for the new resource management: the resource file "%s" (original filename: %s) was not found, but the resource object with uuid %s needs this file.', $resourcePathAndFilename, $resourceInfo['filename'], $resourceInfo['persistence_object_identifier']), 1377704061);
			}
			$md5 = md5_file($resourcePathAndFilename);
			$filesize = filesize($resourcePathAndFilename);

			$this->addSql("UPDATE typo3_flow_resource_resource SET collectionname = " . $this->connection->quote('persistent') .
				", mediatype = " . $this->connection->quote(MediaTypes::getMediaTypeFromFilename($resourceInfo['filename'])) .
				", md5 = " . $this->connection->quote($md5) .
				", filesize = " . $filesize .
				" WHERE persistence_object_identifier = " . $this->connection->quote($resourceInfo['persistence_object_identifier'])
			);

			$newResourcePathAndFilename = FLOW_PATH_DATA . 'Persistent/Resources/' . wordwrap($resourceInfo['sha1'], 5, '/', TRUE) . '/' . $resourceInfo['sha1'];
			if (!file_exists(dirname($newResourcePathAndFilename))) {
				Files::createDirectoryRecursively(dirname($newResourcePathAndFilename));
			}
			$result = @rename($resourcePathAndFilename, $newResourcePathAndFilename);
			if ($result === FALSE) {
				throw new Exception(sprintf('Could not move the data file of resource "%s" from its legacy location at %s to the correct location %s.', $resourceInfo['sha1'], $resourcePathAndFilename, $newResourcePathAndFilename), 1377761373);
			}

		}
		$this->addSql("ALTER TABLE typo3_flow_resource_resource CHANGE mediatype mediatype VARCHAR(100) NOT NULL, CHANGE md5 md5 VARCHAR(32) NOT NULL, CHANGE filesize filesize BIGINT(20) UNSIGNED NOT NULL");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("CREATE TABLE typo3_flow_resource_publishing_abstractpublishingconfiguration (persistence_object_identifier VARCHAR(40) NOT NULL, dtype VARCHAR(255) NOT NULL, PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_flow_resource_resourcepointer (hash VARCHAR(255) NOT NULL, PRIMARY KEY(hash)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

		$resourcesResult = $this->connection->executeQuery('SELECT DISTINCT hash FROM typo3_flow_resource_resource');
		while ($resourceInfo = $resourcesResult->fetch(\PDO::FETCH_ASSOC)) {
			$this->addSql("INSERT INTO typo3_flow_resource_resourcepointer SET hash = " . $this->connection->quote($resourceInfo['hash']));
		}

		$this->addSql("ALTER TABLE typo3_flow_resource_resource ADD publishingconfiguration VARCHAR(40) DEFAULT NULL, CHANGE sha1 resourcepointer VARCHAR(255) NOT NULL, DROP md5, DROP collectionname, DROP mediatype, ADD fileextension VARCHAR(255) NOT NULL, DROP mediatype");
		$this->addSql("CREATE TABLE typo3_flow_security_authorization_resource_securitypublis_861cb (persistence_object_identifier VARCHAR(40) NOT NULL, allowedroles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
		$this->addSql("ALTER TABLE typo3_flow_security_authorization_resource_securitypublis_861cb ADD CONSTRAINT FK_234846D521E3D446 FOREIGN KEY (persistence_object_identifier) REFERENCES typo3_flow_resource_publishing_abstractpublishingconfiguration (persistence_object_identifier) ON DELETE CASCADE");

		// FIXME: The following will fail!
		// FIXME: implement further changes (see up())

		$this->addSql("ALTER TABLE typo3_flow_resource_resource ADD CONSTRAINT typo3_flow_resource_resource_ibfk_1 FOREIGN KEY (resourcepointer) REFERENCES typo3_flow_resource_resourcepointer (hash)");
		$this->addSql("ALTER TABLE typo3_flow_resource_resource ADD CONSTRAINT FK_B4D45B32A4A851AF FOREIGN KEY (publishingconfiguration) REFERENCES typo3_flow_resource_publishing_abstractpublishingconfiguration (persistence_object_identifier)");
		$this->addSql("CREATE INDEX IDX_B4D45B323CB65D1 ON typo3_flow_resource_resource (resourcepointer)");
		$this->addSql("CREATE INDEX IDX_B4D45B32A4A851AF ON typo3_flow_resource_resource (publishingconfiguration)");

		$this->addSql("ALTER TABLE typo3_flow_resource_resource CHANGE mediatype mediatype VARCHAR(100) DEFAULT NULL");
	}
}

?>