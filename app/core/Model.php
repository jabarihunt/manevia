<?php namespace Manevia;

    /********************************************************************************
     * MANEVIA BASE MODEL CLASS
     * This is the model that all other base models (generated) should extend.
     * @author Jabari J. Hunt <jabari@jabari.net>
     ********************************************************************************/

        class Model {

            /********************************************************************************
             * CONSTRUCT METHOD
             *
             * The model constructors should be private.  It should only be called by
             * internal static methods (create, get, etc) to create new model instances.
             *
             * @param mixed $data An array values used to create the model
             ********************************************************************************/

                protected function __construct(array $data) {

                    // ADD DATA TO MODEL IF THE DATA FIELD ALREADY EXISTS

                        foreach ($data as $field => $value) {
                            if (property_exists($this, $field)) {$this->$field = $value;}
                        }

                }

            /********************************************************************************
             * UPDATE METHOD
             *
             * Used by all child models to update their respective table(s). If any values
             * need to be altered first, simply override this method, edit the values as
             * needed, then call `parent::update($values)` at the end of the method.
             *
             * @param mixed $data An array of values to update
             * @return boolean
             ********************************************************************************/

                public function update(array $data): bool {

                    // RUN BEFORE UPDATE CALLBACK

                        $data = call_user_func([$this, 'beforeUpdate'], $data);

                    // SET INITIAL VARIABLES

                        $bindTypes  = '';
                        $set        = '';
                        $updated    = FALSE;

                    // LOOP THROUGH VALUES -> SANITIZE, ADD TO UPDATE QUERY PARTS

                        foreach ($data as $field => $value) {

                            $data[$field] = DB::sanitize($value, static::DATA_TYPES[$field]);
                            $data[$field] = &$data[$field];    // ADDED TO SATISFY THE call_user_func_array() METHOD
                            $set         .= "`{$field}` = ?, ";

                            if (in_array(static::DATA_TYPES[$field], DB::DATA_TYPE_INTEGER)) {
                                $bindTypes .= 'i';
                            } else if (in_array(static::DATA_TYPES[$field], DB::DATA_TYPE_REAL)) {
                                $bindTypes .= 'd';
                            } else {
                                $bindTypes .= 's';
                            }

                        }

                        $set = rtrim($set, ', ');

                    // CREATE PREPARED STATEMENT | ADD BIND TYPE VALUE TO BEGINNING OF VALUES ARRAY

                        $statement = DB::prepare("UPDATE `" . static::TABLE_NAME . "` SET {$set} WHERE `id` = '{$this->id}'");
                        array_unshift($data, $bindTypes);

                    // USE REFLECTION CLASS INSTANCE TO BIND VALUES TO PREPARED STATEMENT | EXECUTE STATEMENT AND PROCESS RESULTS

                        call_user_func_array([&$statement, 'bind_param'], static::arrayReferenceValues($data));
                        $statement->execute();

                        if ($statement->affected_rows == 1) {

                            unset($data[0]);

                            foreach ($data as $field => $value) {
                                $this->$field = $value;
                            }

                            $updated = TRUE;

                        }

                    // RUN AFTER UPDATE CALLBACK | CLOSE STATEMENT

                        if ($statement->errno == 0) {
                            call_user_func([$this, 'afterUpdate']);
                        }

                        $statement->close();

                    // RETURN RESULT

                        return $updated;

                }

            /********************************************************************************
             * DELETE METHOD
             * @returns boolean
             ********************************************************************************/

                public function delete(): bool {

                    call_user_func([$this, 'beforeDelete']);
                    $deleted = self::deleteByIds([$this->id]);
                    call_user_func([$this, 'afterDelete']);
                    return $deleted;

                }

            /********************************************************************************
             * DELETE BY IDS METHOD
             * @param array $ids
             * @return boolean
             ********************************************************************************/

                public static function deleteByIds(array $ids): bool {

                    // SET INITIAL VARIABLES

                        $deleted  = FALSE;
                        $ids      = self::sanitize($ids);

                    // CREATE STRING OF IDS | DELETE FROM THE DATABASE | RETURN RESULT

                        $idString = implode(',', $ids);

                        if (!empty($idString)) {
                            $deleted = DB::query("DELETE FROM `" . static::TABLE_NAME . "` WHERE `id` IN ({$idString})");
                        }

                        return $deleted;

                }

            /********************************************************************************
             * CALLBACK METHODS
             *
             * The model constructors should be private.  It should only be called by
             * internal static methods (create, get, etc) to create new model instances.
             ********************************************************************************/

                protected static function beforeCreate(array $data): array {return $data;}
                protected function afterCreate() {}
                protected static function beforeGet($idOrData) {return $idOrData;}
                protected function afterGet() {}
                protected function beforeUpdate(array $data): array {return $data;}
                protected function afterUpdate() {}
                protected function beforeDelete() {}
                protected function afterDelete() {}

            /********************************************************************************
             * SANITIZE METHOD
             *
             * Sanitize an array of passed values.  Uses the DB::sanitizeValue() method;
             *
             * @param array $data An array values to update
             * @return array
             ********************************************************************************/

                final protected static function sanitize(array $data): array {

                    foreach ($data as $field => $value) {

                        // SET THE FIELD DATA TYPE | SANITIZE FIELD

                            if (is_string($field) && !empty(static::DATA_TYPES[$field])) {
                                $dataType = static::DATA_TYPES[$field];
                            } else {
                                $dataType = null;
                            }

                            $data[$field] = DB::sanitize($value, $dataType);
                    }

                    return $data;

                }

            /********************************************************************************
             * ARRAY REFERENCE VALUES METHOD
             *
             * Returns an array with the values as a reference
             *
             * @param array $data
             * @return array
             ********************************************************************************/

                final protected static function arrayReferenceValues(array $data): array {

                    $referencedValues = [];

                    if (strnatcmp(phpversion(),'5.3') >= 0) {
                        foreach ($data as $key => $value) {$referencedValues[$key] = &$data[$key];}
                    }

                    return $referencedValues;

                }

        }

?>